<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\Item;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function getUnavailableDates(Request $request, Item $item)
    {
        $relatedOrderStamps = OrderItem::query()
            ->whereIn('status', ['in_rent', 'confirmed', 'waiting'])
            ->select(['rent_start_date', 'rent_start_time', 'rent_end_time', 'rent_end_date'])
            ->where('item_id', '=', $item->id)
            ->get();

        $fullyRentedDates = [];

        foreach ($relatedOrderStamps as $rental) {
            $startDateTime = strtotime($rental->rent_start_date.' '.$rental->rent_start_time);
            $endDateTime = strtotime($rental->rent_end_date.' '.$rental->rent_end_time);

            $currentDateTime = $startDateTime;
            while ($currentDateTime < $endDateTime) {
                $currentDate = date('Y-m-d', $currentDateTime);

                if (! isset($fullyRentedDates[$currentDate])) {
                    $fullyRentedDates[$currentDate] = [];
                }

                $timeSpan = date('H:i:s', $currentDateTime);
                $fullyRentedDates[$currentDate][] = $timeSpan;

                $currentDateTime += 300;
            }
        }

        $fullyRentedDates = array_filter($fullyRentedDates, function ($times) {
            return count($times) == 288;
        });

        return response()
            ->json([
                'success' => true,
                'forbiddenDates' => array_keys($fullyRentedDates),
            ]);
    }

    public function getAvailableTimes(Request $request, Item $item)
    {
        $startDate = $request->input('start_date');
        $relatedOrderTimeStamps = DB::table('order_items')
            ->select(['rent_start_date', 'rent_start_time', 'rent_end_time', 'rent_end_date'])
            ->whereIn('status', ['in_rent', 'confirmed', 'waiting'])
            ->where('item_id', '=', $item->id)
            ->get();

        $availableTimes = $this->generateTimeSpans();

        foreach ($relatedOrderTimeStamps as $orderItem) {
            $startRentDate = Carbon::parse($orderItem->rent_start_date.' '.$orderItem->rent_start_time);
            $endRentDate = Carbon::parse($orderItem->rent_end_date.' '.$orderItem->rent_end_time);

            foreach ($availableTimes as $availableTime) {
                if (Carbon::parse($startDate.' '.$availableTime)->between($startRentDate, $endRentDate) && $orderItem->rent_start_date == $startDate && $orderItem->rent_end_date == $startDate) {
                    unset($availableTimes[array_search($availableTime, $availableTimes)]);

                    continue;
                }
                if ($orderItem->rent_start_date == $startDate && $orderItem->rent_end_date != $startDate) {
                    $startTimeSplit = explode(':', $orderItem->rent_start_time);
                    for ($hours = (int) $startTimeSplit[0]; $hours < 24; $hours++) {
                        for ($minutes = (int) $startTimeSplit[1]; $minutes <= 55; $minutes += 5) {
                            unset($availableTimes[array_search(str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT), $availableTimes)]);
                        }
                    }

                    continue;
                }
                if ($orderItem->rent_end_date == $startDate && $orderItem->rent_start_date != $startDate) {
                    $endTimeSplit = explode(':', $orderItem->rent_end_time);
                    for ($minutes = (int) $endTimeSplit[1]; $minutes >= 0; $minutes -= 5) {
                        unset($availableTimes[array_search(str_pad((int) $endTimeSplit[0], 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT), $availableTimes)]);
                    }
                    for ($hours = (int) $endTimeSplit[0] - 1; $hours >= 0; $hours--) {
                        for ($minutes = 55; $minutes >= 0; $minutes -= 5) {
                            unset($availableTimes[array_search(str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT), $availableTimes)]);
                        }
                    }
                }
            }
        }

        $currentTime = now('Asia/Almaty');

        foreach ($availableTimes as $span) {
            $time = Carbon::parse($startDate.' '.$span, 'Asia/Almaty');
            if ($time->lt($currentTime)) {
                unset($availableTimes[array_search($span, $availableTimes)]);
            }
        }

        usort($availableTimes, function ($a, $b) {
            $timeA = strtotime($a);
            $timeB = strtotime($b);

            if ($timeA == $timeB) {
                return 0;
            }

            return ($timeA < $timeB) ? -1 : 1;
        });

        return response()
            ->json([
                'success' => true,
                'availableTimes' => array_values($availableTimes),
            ]);
    }

    public function getUnavailableRentEndDates(Request $request, Item $item)
    {
        $rentStartDate = $request->input('start_date');

        $relatedOrderTimeStamps = DB::table('order_items')
            ->select(['rent_start_date'])
            ->whereIn('status', ['in_rent', 'confirmed', 'waiting'])
            ->where('rent_start_date', '>=', $rentStartDate)
            ->where('item_id', '=', $item->id)
            ->first();

        $availableDates = [];

        if (! is_null($relatedOrderTimeStamps)) {
            $startDate = Carbon::parse($rentStartDate);
            $endDate = Carbon::parse($relatedOrderTimeStamps->rent_start_date);

            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $availableDates[] = $date->format('Y-m-d');
            }
        }

        return response()
            ->json([
                'success' => true,
                'availableDates' => array_values($availableDates),
            ]);
    }

    public function getAvailableRentEndTimespans(Request $request, Item $item)
    {
        $finishDate = $request->input('finish_date');
        $startDate = $request->input('start_date');
        $startTime = $request->input('start_time');
        $relatedOrderTimeStamps = DB::table('order_items')
            ->whereIn('status', ['in_rent', 'confirmed', 'waiting'])
            ->select(['rent_start_date', 'rent_start_time'])
            ->where('item_id', '=', $item->id)
            ->where('rent_start_date', '=', $finishDate)
            ->orderBy('rent_start_time', 'ASC')
            ->first();

        $timeSpans = [];

        if (! is_null($relatedOrderTimeStamps)) {
            $startDateSplitted = explode(':', $relatedOrderTimeStamps->rent_start_time);

            for ($minutes = (int) $startDateSplitted[1]; $minutes >= 0; $minutes -= 5) {
                $timeSpans[] = (str_pad((int) $startDateSplitted[0], 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT));
            }
            for ($hours = (int) $startDateSplitted[0] - 1; $hours >= 0; $hours--) {
                for ($minutes = 55; $minutes >= 0; $minutes -= 5) {
                    $timeSpans[] = (str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT));
                }
            }
        } else {
            $timeSpans = $this->generateTimeSpans();
        }

        if ($startDate === $finishDate) {
            $currentTime = now('Asia/Almaty');
            unset($timeSpans[array_search($startTime, $timeSpans)]);
            foreach ($timeSpans as $span) {
                $time = Carbon::parse($startDate.' '.$span, 'Asia/Almaty');
                if ($time->lt($currentTime)) {
                    unset($timeSpans[array_search($span, $timeSpans)]);
                }
            }

            $startRentTime = Carbon::parse($startDate.' '.$startTime, 'Asia/Almaty');
            foreach ($timeSpans as $span){
                $time = Carbon::parse($finishDate.' '.$span, 'Asia/Almaty');
                if ($time->lt($startRentTime)) {
                    unset($timeSpans[array_search($span, $timeSpans)]);
                }
            }
        }

        usort($timeSpans, function ($a, $b) {
            $timeA = strtotime($a);
            $timeB = strtotime($b);

            if ($timeA == $timeB) {
                return 0;
            }

            return ($timeA < $timeB) ? -1 : 1;
        });

        return response()
            ->json([
                'success' => true,
                'nextAvailableTimes' => $timeSpans,
            ]);
    }

    public function getDefaultTimes(Request $request)
    {
        date_default_timezone_set('Asia/Almaty');

        $startdate = $request->input('start_date');
        $availableTimes = $this->generateTimeSpans();

        $currentDate = date('Y-m-d');
        $currentTime = date('H:i');

        if ($startdate === $currentDate) {
            $availableTimes = array_filter($availableTimes, function($time) use ($currentTime) {
                return $time >= $currentTime;
            });
        }

        return response()->json([
            'success' => true,
            'availableTimes' => array_values($availableTimes),
        ]);
    }

    public function getAvailableItemsByTime(Request $request, int $id)
    {
        $good = Good::query()->find($id);
        $startDate = $request->input('start_date');
        $startTime = $request->input('start_time');
        $endDate = $request->input('end_date');
        $endTime = $request->input('end_time');

        $conflictingItemIds = DB::select("
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.good_id = :good_id
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < :end_date OR (order_items.rent_start_date = :end_date_v2 AND order_items.rent_start_time <= :end_time))
        AND
        (order_items.rent_end_date > :start_date OR (order_items.rent_end_date = :start_date_v2 AND order_items.rent_end_time >= :start_time))
    )
", [
            'good_id' => $good->id,
            'start_date' => $startDate,
            'start_date_v2' => $startDate,
            'start_time' => $startTime,
            'end_date' => $endDate,
            'end_date_v2' => $endDate,
            'end_time' => $endTime,
        ]);
        $conflictingItemIds = array_map(function ($item) {
            return $item->item_id;
        }, $conflictingItemIds);

        $items = $good->items()->whereNotIn('id', $conflictingItemIds)->with('good')->get();

        foreach ($items as $item){
            $item->good->name = $item->good['name_'.session()->get('locale', 'ru')];
        }

        return response()
            ->json(['available_items' => $items]);
    }

    public function generateTimeSpans()
    {
        $timeSpans = [];
        for ($hours = 0; $hours < 24; $hours++) {
            for ($minutes = 0; $minutes < 60; $minutes += 5) {
                $timeSpans[] = str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT);
            }
        }

        return $timeSpans;
    }
}
