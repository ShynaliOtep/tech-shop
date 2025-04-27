<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Order;
use App\Orchid\Layouts\Dashboard\SalesDataChartLayout;
use Illuminate\Support\Carbon;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class DashboardScreen extends Screen
{
    public $name = 'Аналитика';

    public function query(): array
    {
        $cityId = City::getPlatformCity();

        $from = request()->get('date_from', Carbon::today()->toDateString());
        $to = request()->get('date_to', Carbon::today()->toDateString());

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        $daysDiff = $start->diffInDays($end);

        if ($daysDiff === 0) {
            $groupBy = '%Y-%m-%d %H'; // По часам
        } elseif ($daysDiff <= 90) {
            $groupBy = '%Y-%m-%d'; // По дням
        } else {
            $groupBy = '%Y-%m'; // По месяцам
        }

        $salesData = Order::where('city_id', $cityId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("strftime(?, created_at) as period, SUM(amount_paid) as total_sales", [$groupBy])
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('total_sales', 'period')
            ->toArray();

        $unpaidSales = Order::where('city_id', City::getPlatformCity())->whereBetween('created_at',[$start->startOfDay(), $end->endOfDay()])->where('paid_status', 'unpaid')->sum('amount_unpaid');
        $unpaidOrders = Order::where('city_id', City::getPlatformCity())->whereBetween('created_at',[$start->startOfDay(), $end->endOfDay()])->where('paid_status', 'unpaid')->get();

//      dump( [
//        'labels' => array_keys($salesData),
//        'data' => array_values($salesData),
//    ]);


        return [
            'salesChart' => [
                [
                    'labels' => array_keys($salesData),
                    'values' => array_values($salesData),
                ]
            ],

            'totalSales' => array_sum($salesData),
            'unpaidSales' => $unpaidSales,
            'unpaidOrders' => $unpaidOrders,
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
        ];
    }


    public function layout(): array
    {
        return [
            Layout::rows([
                DateRange::make('date_range')
                    ->title('Выберите диапазон дат')
                    ->value([
                        'start' => $this->query()['date_from'],
                        'end' => $this->query()['date_to'],
                    ]),

                Button::make('Применить')
                    ->method('applyFilter')
                    ->type(Color::PRIMARY),
            ]),

            Layout::metrics([
                'Итого за выбранный период' => 'totalSales',
                'Неоплаченные заказы' => 'unpaidSales',
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

            SalesDataChartLayout::make('salesChart', 'График продаж')
        ];
    }

    public function applyFilter()
    {
        return redirect()->route('platform.analytics', [
            'date_from' => request('date_range.start'),
            'date_to' => request('date_range.end'),
        ]);
    }
}
