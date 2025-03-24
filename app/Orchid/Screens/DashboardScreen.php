<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
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
        dd($salesData);

        return [
            'salesChart' => [
                'labels' => array_keys($salesData),
                'data' => array_values($salesData),
            ],
            'totalSales' => array_sum($salesData),
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
            ]),

            Layout::chart('salesChart')
                ->title('График продаж')
                ->type('line'),
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
