<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Services\Bonus\GoodService;
use App\Services\Good\GoodItemService;
use App\Services\Good\TimeRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        $itemIds = [];
        $counts = [];

        foreach ($cartData as $key => $value) {
            $itemIds[] = $key;
            $counts[$key] = $value['quantity'];
        }

        $items = Good::query()->whereIn('id', $itemIds)->get();

        $client = Auth::guard('clients')->check()
            ? Client::query()->find(Auth::guard('clients')->id())->toArray()
            : null;

        return response()->json(compact('items', 'cartData', 'client', 'counts'))
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function cartItems(Request $request)
    {
        $itemIds = $request->get('items');

        $items = Good::query()->whereIn('id', $itemIds)->with('attachment')->get();

        return response()->json([
            'items' => $items,
        ]);
    }


    public function getAvailableItemsByCount(int $id, Request $request)
    {
        $startDate = $request->post('start_date');
        $startTime = $request->post('start_time');
        $endDate = $request->post('end_date');
        $endTime = $request->post('end_time');

        $service = new GoodItemService();

        $result = $service->getAvailableCountByTime(
            new TimeRange(
                \Illuminate\Support\Carbon::parse($startDate. ' '. $startTime . ':00'),
                \Illuminate\Support\Carbon::parse($endDate. ' '. $endTime . ':00'),
            ),
            $id,
        );

        return response()->json([
            'quantity' => $result,
        ]);
    }


    public function getAvailableAdditionals(Request $request)
    {
        $startDateString = $request->input('startDate');
        $startTimeString = $request->input('startTime');
        $endDateString = $request->input('endDate');
        $endTimeString = $request->input('endTime');
        $startDateTimeString = $startDateString.' '.$startTimeString;
        $endDateTimeString = $endDateString.' '.$endTimeString;
        $goodId = $request->input('goodId');
        $good = Good::query()->findOrFail($goodId);

        $additionalIds = $good->additionals;
        dd($additionalIds);
        if (count($additionalIds) < 0) {
            return response()
                ->json([
                    'success' => true,
                    'additionals' => [],
                ]);
        }
        $additionalItemsIds = Item::query()->whereIn('good_id', $additionalIds)->pluck('id')->toArray();

        $startDateTime = Carbon::parse($startDateTimeString);

        $startDate = $startDateTime->toDateString();

        $endDateTime = Carbon::parse($endDateTimeString);

        $endDate = $endDateTime->toDateString();

        $placeholders = implode(',', array_fill(0, count($additionalItemsIds), '?'));

        $sql = "
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.id IN ($placeholders)
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < ? OR (order_items.rent_start_date = ? AND order_items.rent_start_time <= ?))
        AND
        (order_items.rent_end_date > ? OR (order_items.rent_end_date = ? AND order_items.rent_end_time >= ?))
    )
";

        $params = array_merge(
            $additionalItemsIds,
            [
                $endDateString,
                $endDateString,
                $endTimeString,
                $startDateString,
                $startDateString,
                $startTimeString
            ]
        );
        $unavailableOrderItemsIds = DB::select($sql, $params);

        $unavailableOrderItemsIds = array_map(function ($item) {
            return $item->item_id;
        }, $unavailableOrderItemsIds);

        $unavailableAdditionalIds = Item::query()
            ->whereIn('id', $unavailableOrderItemsIds)
            ->pluck('good_id')->toArray();

        $availableGoods = Item::query()
            ->select('good_id', DB::raw('MAX(id) as id'))
            ->whereIn('good_id', $good->additionals)
            ->groupBy('good_id')
            ->with('good')
            ->get();

        $availableGoods->each(function ($availableGood) use ($unavailableAdditionalIds) {
            $availableGood->available = ! in_array($availableGood->good_id, $unavailableAdditionalIds);
        });

        $availableGoods = $availableGoods->toArray();

        $responseData = [
            'success' => true,
            'additionals' => $availableGoods,
        ];

        return response()->json($responseData);
    }


}
