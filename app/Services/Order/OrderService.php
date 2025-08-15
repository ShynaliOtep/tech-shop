<?php

namespace App\Services\Order;

use App\Models\Good;
use App\Models\Order;

class OrderService
{
    public static function getFirstImage(Order $order): ?string
    {
        $item = $order->items->first();
        if (!$item) {
            return asset('img/no-image.jpg');
        }

        $goodId = $order->items->first()->good_id;

        if (!$goodId) {
            return asset('img/no-image.jpg');
        }

        $good = Good::find($goodId);
        $attachmentUrl = $good->attachment()?->first()?->url;
        if (!$attachmentUrl) {
            return asset('img/no-image.jpg');
        }

        return $attachmentUrl;
    }

    public static function getOrderText(Order $order, int $maxSymbol = 200)
    {
        $itemNames = [];
        foreach ($order->items as $item){
            $name = 'name_'.session()->get('locale', 'ru');
            $itemNames[] = $item->good->$name;
        }
        $text = implode(', ', $itemNames);

        if (strlen($text) > $maxSymbol) {
            $text = substr($text, 0, $maxSymbol - 3) . '...';
        }

        return $text;
    }

    public static function getLastOrder(int $clientId): ?Order
    {
        return Order::where('client_id', $clientId)->orderBy('created_at', 'desc')->first();
    }

    public static function calculateTotalSum(Order $order): void
    {
        $orderItems = $order->orderItems;
        $totalSum = 0;
        foreach ($orderItems as $orderItem) {
            $totalSum += $orderItem->amount_paid;
        }
        $order->amount_paid = $totalSum;
        $order->save();
    }

    public static function calculateRentDates(Order $order): void
    {
        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');
        $order->save();
    }
}
