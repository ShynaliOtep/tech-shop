<?php

namespace App\Services\Good;

use App\Models\Good;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\Set;
use App\Services\City\CityService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Доступность товара для витрины: бейджи в каталоге и занятые дни в календаре.
 *
 * Это подсказка покупателю, а не проверка брони: окончательно доступность
 * проверяет GoodItemService при запросе количества и при оформлении.
 */
class GoodAvailability
{
    /** @var array<int, int> good_id => свободно сейчас */
    private static array $freeNow = [];

    /**
     * Заранее посчитать «свободно сейчас» для списка товаров двумя запросами,
     * чтобы карточки каталога не делали запросы по одной.
     *
     * @param iterable<Good> $goods
     */
    public static function warm(iterable $goods): void
    {
        $goodIds = [];
        $setMembers = [];

        foreach ($goods as $good) {
            if ($good->is_set) {
                $set = Set::query()->where('good_id', $good->id)->first();
                $members = $set ? $set->goods->pluck('id')->all() : [$good->id];
                $setMembers[$good->id] = $members;
                array_push($goodIds, ...$members);
            } else {
                $goodIds[] = $good->id;
            }
        }

        $counts = self::countFreeNow(array_values(array_unique($goodIds)));

        foreach ($goods as $good) {
            self::$freeNow[$good->id] = isset($setMembers[$good->id])
                ? min(array_map(fn ($id) => $counts[$id] ?? 0, $setMembers[$good->id]))
                : ($counts[$good->id] ?? 0);
        }
    }

    public static function freeNow(Good $good): int
    {
        if (! array_key_exists($good->id, self::$freeNow)) {
            self::warm([$good]);
        }

        return self::$freeNow[$good->id];
    }

    /**
     * Дни, в которые все экземпляры товара хотя бы частично заняты.
     * В такой день, возможно, остаются свободные часы — поэтому день не запрещаем,
     * а только помечаем; точный ответ даёт проверка по времени.
     *
     * @return string[] даты Y-m-d
     */
    public static function busyDays(Good $good, Carbon $from, Carbon $to): array
    {
        $goodIds = [$good->id];
        if ($good->is_set) {
            $set = Set::query()->where('good_id', $good->id)->first();
            $goodIds = $set ? $set->goods->pluck('id')->all() : $goodIds;
        }

        $busy = [];
        foreach ($goodIds as $goodId) {
            // Для набора день занят, если занят хотя бы один из его товаров.
            $busy = array_merge($busy, self::busyDaysForGood($goodId, $from, $to));
        }

        $busy = array_values(array_unique($busy));
        sort($busy);

        return $busy;
    }

    /**
     * @return string[]
     */
    private static function busyDaysForGood(int $goodId, Carbon $from, Carbon $to): array
    {
        $itemIds = Item::query()
            ->where('good_id', $goodId)
            ->where('city_id', CityService::city())
            ->where('status', 'available')
            ->pluck('id')
            ->all();

        $allDays = [];
        for ($day = $from->copy()->startOfDay(); $day <= $to; $day->addDay()) {
            $allDays[] = $day->format('Y-m-d');
        }

        if (empty($itemIds)) {
            return $allDays;
        }

        $bookings = OrderItem::query()
            ->whereIn('item_id', $itemIds)
            ->whereIn('status', ['in_rent', 'waiting', 'confirmed'])
            ->where('rent_start_date', '<=', $to->format('Y-m-d'))
            ->where('rent_end_date', '>=', $from->format('Y-m-d'))
            ->get(['item_id', 'rent_start_date', 'rent_end_date']);

        // день => экземпляры, занятые в этот день
        $busyItemsByDay = [];
        foreach ($bookings as $booking) {
            $start = max(Carbon::parse($booking->rent_start_date)->format('Y-m-d'), $from->format('Y-m-d'));
            $end = min(Carbon::parse($booking->rent_end_date)->format('Y-m-d'), $to->format('Y-m-d'));

            for ($day = Carbon::parse($start); $day->format('Y-m-d') <= $end; $day->addDay()) {
                $busyItemsByDay[$day->format('Y-m-d')][$booking->item_id] = true;
            }
        }

        $total = count($itemIds);

        return array_keys(array_filter($busyItemsByDay, fn ($items) => count($items) >= $total));
    }

    /**
     * @param int[] $goodIds
     * @return array<int, int>
     */
    private static function countFreeNow(array $goodIds): array
    {
        if (empty($goodIds)) {
            return [];
        }

        $items = Item::query()
            ->whereIn('good_id', $goodIds)
            ->where('city_id', CityService::city())
            ->where('status', 'available')
            ->get(['id', 'good_id']);

        // Брони хранятся во времени пунктов выдачи (Казахстан), а приложение работает в UTC.
        $now = now('Asia/Almaty');
        $today = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // Та же граница, что в GoodItemService::sqlQuery: бронь, которая начинается
        // или заканчивается ровно сейчас, считается занятостью.
        $busyItemIds = OrderItem::query()
            ->whereIn('item_id', $items->pluck('id'))
            ->whereIn('status', ['in_rent', 'waiting', 'confirmed'])
            ->where(function ($query) use ($today, $time) {
                $query->where('rent_start_date', '<', $today)
                    ->orWhere(fn ($q) => $q->where('rent_start_date', $today)->where('rent_start_time', '<=', $time));
            })
            ->where(function ($query) use ($today, $time) {
                $query->where('rent_end_date', '>', $today)
                    ->orWhere(fn ($q) => $q->where('rent_end_date', $today)->where('rent_end_time', '>=', $time));
            })
            ->pluck('item_id')
            ->flip();

        /** @var Collection $free */
        $free = $items->reject(fn ($item) => $busyItemIds->has($item->id));

        return $free->countBy('good_id')->all();
    }
}
