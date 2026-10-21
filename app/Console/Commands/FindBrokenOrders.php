<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class FindBrokenOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:find-broken
                            {--all : Включить отменённые и возвращённые заказы}
                            {--since= : Только заказы, созданные с этой даты (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заказы с сайта, оформление которых оборвалось: нет суммы и дат аренды';

    /**
     * Оформление с сайта записывает сумму и даты аренды в самом конце.
     * Если дат нет, заказ остался недооформленным: менеджер не получил
     * уведомление, а позиции в статусе waiting держат технику занятой.
     * Договор на такой заказ может появиться позже (подпись клиента
     * в профиле, кнопка в списке заказов), поэтому по нему не фильтруем.
     */
    public function handle()
    {
        $orders = Order::query()
            ->whereNull('rent_start_date')
            ->when(! $this->option('all'), function (Builder $query) {
                $query->whereNotIn('status', ['cancelled', 'returned']);
            })
            ->when($this->option('since'), function (Builder $query, $since) {
                $query->where('created_at', '>=', $since);
            })
            ->with(['owner', 'orderItems'])
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Недооформленных заказов нет.');

            return self::SUCCESS;
        }

        $rows = $orders->map(function (Order $order) {
            $items = $order->orderItems;
            $blocking = $items->whereIn('status', ['waiting', 'confirmed', 'in_rent']);

            return [
                $order->id,
                $order->created_at?->format('Y-m-d H:i'),
                $order->owner?->name ?? '—',
                $order->owner?->phone ?? '—',
                $order->status,
                $order->agreement_id ? 'есть' : 'нет',
                $items->count(),
                $blocking->count(),
                $items->isEmpty() ? '—' : $items->min('rent_start_date').' — '.$items->max('rent_end_date'),
                $items->sum('amount_paid'),
            ];
        });

        $this->table(
            ['Заказ', 'Создан', 'Клиент', 'Телефон', 'Статус', 'Договор', 'Позиций', 'Держат технику', 'Даты позиций', 'Сумма позиций'],
            $rows
        );

        $withItems = $orders->filter(fn (Order $order) => $order->orderItems->isNotEmpty())->count();

        $this->line(sprintf(
            'Всего: %d, из них с позициями: %d, пустых: %d.',
            $orders->count(),
            $withItems,
            $orders->count() - $withItems
        ));

        return self::SUCCESS;
    }
}
