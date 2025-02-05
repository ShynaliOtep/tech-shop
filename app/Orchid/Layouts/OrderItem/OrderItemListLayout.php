<?php

namespace App\Orchid\Layouts\OrderItem;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\NumberRange;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderItemListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orderItems';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', __('translations.Name'))
                ->sort()
                ->render(function (OrderItem $orderItemItem) {
                    return Link::make($orderItemItem->item->good['name_'.session()->get('locale', 'ru')])
                        ->route('platform.orderItems.edit', $orderItemItem);
                }),

            TD::make('order_id', __('translations.Order'))
                ->sort()
                ->filter(
                    Relation::make()
                        ->fromModel(Order::class, 'id')
                )
                ->render(function (OrderItem $orderItemItem) {
                    return Link::make($orderItemItem->order->id)
                        ->route('platform.orders.edit', $orderItemItem->order->id);
                }),

            TD::make('status', __('translations.Status'))
                ->sort()
                ->filter(
                    Select::make('status')
                        ->options([
                            null => __('translations.not chosen'),
                            'returned' => __('translations.returned'),
                            'in_rent' => __('translations.in_rent'),
                            'waiting' => __('translations.waiting'),
                            'confirmed' => __('translations.confirmed'),
                            'cancelled' => __('translations.cancelled'),
                        ])
                        ->title(__('translations.Status'))
                )
                ->render(function (OrderItem $orderItemItem) {
                    return __('translations.'.$orderItemItem->status);
                }),

            TD::make('amount_paid', __('translations.Amount paid'))
                ->sort()
                ->filter(
                    NumberRange::make('amount_paid')
                ),

            TD::make('rent_start_date', __('translations.Rent start date'))
                ->align(TD::ALIGN_RIGHT)
                ->filter(
                    DateRange::make('rent_start_date')
                        ->title(__('translations.Rent start'))
                )
                ->sort(),

            TD::make('rent_start_time', __('translations.Rent start time'))
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('rent_end_date', __('translations.Rent end date'))
                ->align(TD::ALIGN_RIGHT)
                ->filter(
                    DateRange::make('rent_end_date')
                        ->title(__('translations.Rent end'))
                )
                ->sort(),

            TD::make('rent_end_time', __('translations.Rent end time'))
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('translations.Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),
        ];
    }
}
