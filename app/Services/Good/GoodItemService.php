<?php

namespace App\Services\Good;

use App\Models\Good;
use App\Models\Item;
use App\Services\City\CityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
class GoodItemService
{
    /**
     * @param TimeRange $timeRange
     * @param int $goodId
     * @param int $quantity
     * @return array|Item[]
     */
    public function getAvailableGoodItems(TimeRange $timeRange, int $goodId, int $quantity): array
    {
        $items = $this->sqlQuery($timeRange, $goodId);
        Log::info('Available check goodId - ' . $goodId . ' quantity - ' . $quantity .
                        ' timeRange - ' . json_encode($timeRange));
        if (count($items) < $quantity) {
            throw new \Exception('Не хватает товары в наличие');
        }

        return  count($items) == $quantity ? $items : array_slice($items, 0, $quantity);
    }

    public function isAvailableByTime(TimeRange $timeRange, int $goodId, int $quantity, $excludeOrderItems = []): bool
    {
        $items = $this->sqlQuery($timeRange, $goodId, $excludeOrderItems);
        return count($items) >= $quantity;
    }

    public function getAvailableCountByTime(TimeRange $timeRange, int $goodId): int
    {
        $items = $this->sqlQuery($timeRange, $goodId);
        return count($items);
    }

    /**
     * @param TimeRange $timeRange
     * @param int $goodId
     * @return array| Item[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function sqlQuery(TimeRange $timeRange, int $goodId, $excludeOrderItems = []): array
    {
        $good = Good::query()->find($goodId);
        // Город определяем тем же способом, что и при выборке доступных экземпляров ниже,
        // иначе при пустой сессии (select_city не выбран) подзапрос конфликтов сравнивает
        // city_id с NULL, не находит занятых броней и товар считается полностью доступным.
        $cityId = CityService::city();

        $bindings = [
            'good_id' => $good->id,
            'city_id' => $cityId,
            'start_date' => $timeRange->start->format('Y-m-d'),
            'start_date_v2' =>  $timeRange->start->format('Y-m-d'),
            'start_time' => $timeRange->start->format('H:i:s'),
            'end_date' =>  $timeRange->end->format('Y-m-d'),
            'end_date_v2' =>  $timeRange->end->format('Y-m-d'),
            'end_time' => $timeRange->end->format('H:i:s'),
        ];

        // Корректный биндинг списка исключаемых order_items: каждый id — отдельный плейсхолдер.
        $excludeClause = '';
        $excludeOrderItems = array_values(array_filter($excludeOrderItems));
        if (! empty($excludeOrderItems)) {
            $placeholders = [];
            foreach ($excludeOrderItems as $i => $excludeId) {
                $key = 'exclude_' . $i;
                $placeholders[] = ':' . $key;
                $bindings[$key] = $excludeId;
            }
            $excludeClause = 'AND order_items.id NOT IN (' . implode(', ', $placeholders) . ')';
        }

        $conflictingItemIds = DB::select("
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.good_id = :good_id
    AND items.city_id = :city_id
    $excludeClause
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < :end_date OR (order_items.rent_start_date = :end_date_v2 AND order_items.rent_start_time <= :end_time))
        AND
        (order_items.rent_end_date > :start_date OR (order_items.rent_end_date = :start_date_v2 AND order_items.rent_end_time >= :start_time))
    )
", $bindings);
        $conflictingItemIds = array_map(function ($item) {
            return $item->item_id;
        }, $conflictingItemIds);

        $items = $good->items()
            ->where('city_id', CityService::city())
            ->whereNotIn('id', $conflictingItemIds)
            ->where('status', '=', 'available')
            ->with('good')
            ->get();

        $result = [];
        foreach ($items as $item){
            $item->good->name = $item->good['name_'.session()->get('locale', 'ru')];
            $result[] = $item;
        }

        return $result;
    }
}
