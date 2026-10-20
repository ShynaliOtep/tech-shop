<?php

namespace App\Services\Order;

use App\Models\Item;

/**
 * Блокировка экземпляров на время оформления брони.
 *
 * Проверка «свободен ли экземпляр» и создание OrderItem — два отдельных запроса.
 * Без блокировки два одновременных оформления видят один и тот же свободный экземпляр
 * и оба его бронируют. Вызывать строго внутри DB::transaction и до проверки доступности:
 * второй запрос ждёт, пока первый закоммитит свои брони, и уже видит их.
 *
 * Строки блокируются в порядке id, чтобы два заказа с одинаковым набором товаров
 * не взаимоблокировали друг друга.
 */
class BookingLock
{
    /**
     * @param int[] $goodIds товары, экземпляры которых будут подбираться
     */
    public static function goods(array $goodIds): void
    {
        $goodIds = array_values(array_unique(array_filter(array_map('intval', $goodIds))));

        if (empty($goodIds)) {
            return;
        }

        Item::query()
            ->whereIn('good_id', $goodIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id');
    }

    /**
     * @param int[] $itemIds конкретные экземпляры, которые будут забронированы
     */
    public static function items(array $itemIds): void
    {
        $itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds))));

        if (empty($itemIds)) {
            return;
        }

        Item::query()
            ->whereIn('id', $itemIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id');
    }
}
