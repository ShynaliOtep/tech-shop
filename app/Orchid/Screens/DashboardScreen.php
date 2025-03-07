<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class DashboardScreen extends Screen
{
    public $name = 'Аналитика';

    public function query(): array
    {
        $todaySales = Order::where('city_id', City::getPlatformCity())->whereBetween('created_at',[Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->sum('amount_paid');
        $unpaidSales = Order::where('city_id', City::getPlatformCity())->whereBetween('created_at',[Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->where('paid_status', 'unpaid')->sum('amount_unpaid');
        $totalSales = $todaySales + $unpaidSales;
        $unpaidOrders = Order::where('city_id', City::getPlatformCity())->whereBetween('created_at',[Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->where('paid_status', 'unpaid')->get();

        return [
            'todaySales' => $todaySales,
            'unpaidSales' => $unpaidSales,
            'totalSales' => $totalSales,
            'unpaidOrders' => $unpaidOrders,
        ];
    }

    public function name(): ?string
    {
        return "Продажи за сегодня: {$this->query()['todaySales']} тенге (Неоплаченные: {$this->query()['unpaidSales']} тенге)";
    }

    public function layout(): array
    {
        return [
            Layout::metrics([
                'Сегодняшняя касса' => 'todaySales',
                'Неоплаченные заказы' => 'unpaidSales',
                'Итого' => 'totalSales',
            ]),

            Layout::table('unpaidOrders', [
                TD::make('id', 'ID')
                    ->render(function ($order) {
                        return Link::make($order->id)
                            ->route('platform.orders.edit', $order->id);
                    }),
                TD::make('customer_name', 'Клиент')->render(fn ($order) => $order->owner->name),
                TD::make('amount_paid', 'Сумма к оплате')->render(fn ($order) => number_format($order->amount_paid, 0, ',', ' ') . ' ₸'),
                TD::make('amount_unpaid', 'Не оплаченная сумма')->render(fn ($order) => number_format($order->amount_unpaid, 0, ',', ' ') . ' ₸'),
                TD::make('created_at', 'Дата')->render(fn ($order) => $order->created_at->format('d.m.Y H:i')),
            ])->title('Неоплаченные заказы'),
        ];
    }
}
