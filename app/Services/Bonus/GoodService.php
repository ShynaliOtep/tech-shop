<?php

namespace App\Services\Bonus;

use App\Models\Good;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class GoodService
{
    public function getAvailableItems(
        int $goodId,
        string $startDate,
        string $startTime,
        string $endDate,
        string $endTime,
    )
    {
        $good = Good::query()->find($goodId);

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

        return $items;
    }

    public function getAllAvailableItems(
        string $startDate,
        string $startTime,
        string $endDate,
        string $endTime,
    )
    {
        $conflictingItemIds = DB::select("
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < :end_date OR (order_items.rent_start_date = :end_date_v2 AND order_items.rent_start_time <= :end_time))
        AND
        (order_items.rent_end_date > :start_date OR (order_items.rent_end_date = :start_date_v2 AND order_items.rent_end_time >= :start_time))
    )
", [
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

        $items = Item::whereNotIn('id', $conflictingItemIds)->with('good')->pluck('good.name_ru', 'id');

//        foreach ($items as $item){
//            $item->good->name = $item->good['name_'.session()->get('locale', 'ru')];
//        }

        return $items;
    }
}
