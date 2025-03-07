<?php

namespace App\Orchid\Layouts\Order;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\NumberRange;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;

class OrderListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orders';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', __('translations.OrderID'))
                ->filter(Input::make())

                ->render(function (Order $order) {
                    return Link::make($order->id)
                        ->route('platform.orders.edit', $order->id);
                }),
            TD::make('client_id', __('translations.Client'))
                ->filter(
                    Relation::make()
                        ->fromModel(Client::class, 'name')
                )
                ->render(function (Order $order) {
                    return Link::make($order->owner->name)
                        ->route('platform.clients.edit', $order->owner);
                }),
            TD::make('amount_paid', __('translations.Amount paid'))
                ->filter(
                    NumberRange::make()
                ),
            TD::make('amount_unpaid', __('translations.amountUnpaid'))
                ->filter(
                    NumberRange::make()
                ),
            TD::make('agreement_id', __('translations.Agreement id'))
                ->filter(Input::make()),

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
                        ->title('status')
                        ->help(__('translations.Status'))
                )
                ->render(function (Order $order) {
                    return __('translations.'.$order->status);
                }),

            TD::make('paid_status', __('Статус оплаты'))
                ->sort()
                ->filter(
                    Select::make('paid_status')
                        ->options([
                            null => __('translations.not chosen'),
                            'pending' => __('translations.pending'),
                            'paid' => __('translations.paid'),
                            'unpaid' => __('translations.unpaid'),
                        ])
                        ->title('paid_status')
                )
                ->render(function (Order $order) {
                    return __('translations.'.$order->paid_status);
                }),

            TD::make('created_at', __('translations.Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('updated_at', __('translations.Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('rent_start_date', __('translations.Rent start date'))
                ->align(TD::ALIGN_RIGHT)
                ->filter(
                    DateRange::make('rent_start_date')
                        ->title(__('translations.Rent start'))
                )
                ->sort(),
            TD::make('rent_end_date', __('translations.Rent end date'))
                ->align(TD::ALIGN_RIGHT)
                ->filter(
                    DateRange::make('rent_end_date')
                        ->title(__('translations.Rent end'))
                )
                ->sort(),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (\App\Models\Order $order) {
                    $btnsList = [
                        Link::make(__('translations.Look at items'))
                            ->route('platform.orderItems.list',
                                [
                                    'filter[order_id]' => $order->id,
                                ]
                            )
                            ->icon('bs.search'),
                    ];

                    $btnsList[] = Button::make(__('translations.Make agreement'))
                        ->icon('bs.file-text')
                        ->confirm('Вы действительно хотите сформировать договор для этого заказа?')
                        ->method('makeAgreement', [
                            'id' => $order->id,
                        ]);

                    if ($order->status === 'in_rent') {
                        $btnsList[] = Button::make(__('translations.Return'))
                            ->icon('bs.arrow-return-left')
                            ->confirm(__('If you return this order, you will not be available to use it again'))
                            ->method('return', [
                                'id' => $order->id,
                            ]);
                    }

                    if ($order->status === 'waiting') {
                        $btnsList[] = Link::make(__('translations.Edit'))
                            ->route('platform.orders.edit', $order->id)
                            ->icon('bs.pencil');
                        $btnsList[] = Button::make(__('translations.Confirm'))
                            ->icon('bs.check2')
                            ->confirm(__('Would you like to confirm this order?'))
                            ->method('confirm', [
                                'id' => $order->id,
                            ]);
                        $btnsList[] = Button::make(__('translations.Cancel'))
                            ->icon('bs.x')
                            ->confirm(__('If you cancel this order, you will not be available to use it again'))
                            ->method('cancel', [
                                'id' => $order->id,
                            ]);
                    }

                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list($btnsList);
                }
                ),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function remove(Order $order)
    {
        $order->delete();

        Alert::info('You have successfully deleted the orders.');

        return redirect()->route('platform.orders.list');
    }
}
