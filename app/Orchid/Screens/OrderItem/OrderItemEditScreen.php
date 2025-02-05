<?php

namespace App\Orchid\Screens\OrderItem;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrderItemEditScreen extends Screen
{
    /**
     * @var OrderItem
     */
    public $orderItem;

    /**
     * Query data.
     */
    public function query(OrderItem $orderItem): array
    {
        return [
            'orderItem' => $orderItem,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->orderItem->exists ? __('translations.Edit orderItem') : __('translations.Creating a new orderItem');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.OrderItems');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->orderItem->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->orderItem->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->orderItem->exists),
        ];
    }

    public function layout(): array
    {
        $itemOptions = Item::all()->pluck('name', 'id')->toArray();

        return [

            Layout::rows([

                Select::make('orderItem.item_id')
                    ->options(
                        $itemOptions
                    )
                    ->help(__('translations.OrderItem item help'))
                    ->required()
                    ->title(__('translations.Item')),

                Relation::make('orderItem.order_id')
                    ->fromModel(Order::class, 'id')
                    ->required()
                    ->help(__('translations.OrderItem order help'))
                    ->title(__('translations.Order')),

                Select::make('orderItem.additionals')
                    ->options(
                        $itemOptions
                    )
                    ->multiple()
                    ->help(__('translations.OrderItem additional help'))
                    ->title(__('translations.Additionals')),

                Select::make('orderItem.status')
                    ->options([
                        'returned' => __('translations.returned'),
                        'in_rent' => __('translations.in_rent'),
                        'waiting' => __('translations.waiting'),
                        'confirmed' => __('translations.confirmed'),
                        'cancelled' => __('translations.cancelled'),
                    ])
                    ->title(__('translations.Status'))
                    ->required()
                    ->help(__('translations.OrderItem status help')),

                Select::make('orderItem.is_additional')
                    ->options([
                        false => 'Нет',
                        true => 'Да',
                    ])
                    ->title(__('translations.Is additional'))
                    ->required()
                    ->help(__('translations.OrderItem is_additional help')),

                DateTimer::make('orderItem.rent_start_date')
                    ->title(__('translations.Rent start date'))
                    ->placeholder(__('translations.OrderItem rent_start help'))
                    ->required()
                    ->help(__('translations.OrderItem rent_start help'))
                    ->format('Y-m-d'),

                Select::make('orderItem.rent_start_time')
                    ->options($this->generateTimeSpans())
                    ->required()
                    ->title(__('translations.Rent start time'))
                    ->help(__('translations.OrderItem rent_start_time help')),

                DateTimer::make('orderItem.rent_end_date')
                    ->title(__('translations.Rent end date'))
                    ->required()
                    ->placeholder(__('translations.OrderItem rent_end help'))
                    ->help(__('translations.OrderItem rent_end help'))
                    ->format('Y-m-d'),

                Select::make('orderItem.rent_end_time')
                    ->options($this->generateTimeSpans())
                    ->required()
                    ->title(__('translations.Rent end time'))
                    ->help(__('translations.OrderItem rent_end_time help')),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function createOrUpdate(OrderItem $orderItem, Request $request)
    {
        $orderId = $request->input('orderItem')['order_id'];

        $item = Item::find($request->input('orderItem')['item_id']);

        $order = Order::query()->find($orderId);

        $client = $order->owner;

        $orderItem->fill($request->input('orderItem'));

        if ($orderItem->exists && !$orderItem->is_additional){
            $order->amount_paid = $order->amount_paid - $orderItem->amount_paid;
            $order->save();
        }

        $dateObj1 = DateTime::createFromFormat('Y-m-d H:i:s', $request->all()['orderItem']['rent_start_date'].' '.$request->all()['orderItem']['rent_start_time']);
        $dateObj2 = DateTime::createFromFormat('Y-m-d H:i:s', $request->all()['orderItem']['rent_end_date'].' '.$request->all()['orderItem']['rent_end_time']);

        $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

        $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

        $diffInDays = max(1, $diffInDays);

        $orderItem->amount_of_days = $diffInDays;

        $goodAmount = 0;

        $goodAmount += $item->good->discount_cost ?? $item->good->cost;

        $goodAmount *= $diffInDays;

        $totalAmount = $goodAmount;

        $orderItem->amount_paid = $goodAmount;

        $orderItem->additionals = $request->input('orderItem.additionals', []);

        if ($orderItem->exists && count($orderItem->additionals) != 0) {
            $totalAmount = 0;
            $order->amount_paid = $order->amount_paid - OrderItem::query()->where('parent_order_item_id', '=', $orderItem->id)->sum('amount_paid');

            $order->save();
            OrderItem::query()->where('parent_order_item_id', '=', $orderItem->id)->delete();

            $orderItem->additionals = $request->input('orderItem')['additionals'] ?? [];
        }

        $orderItem->save();

        $parentOrderItemAdditionalsId = [];

        if (count($orderItem->additionals) != 0) {
            $orderItem->is_additional = false;
            foreach ($orderItem->additionals as $additionalId) {
                $additional = Item::find($additionalId);
                $childOrderItem = OrderItem::query()->create([
                    'order_id' => $order->id,
                    'item_id' => $additionalId,
                    'additionals' => [],
                    'is_additional' => true,
                    'status' => $orderItem->status,
                    'amount_of_days' => $diffInDays,
                    'parent_order_item_id' => $orderItem->id,
                    'amount_paid' => ($additional->good->additional_cost ?? $additional->good->cost) * $diffInDays,
                    'rent_start_date' => $request->input('orderItem')['rent_start_date'],
                    'rent_start_time' => $request->input('orderItem')['rent_start_time'],
                    'rent_end_date' => $request->input('orderItem')['rent_end_date'],
                    'rent_end_time' => $request->input('orderItem')['rent_end_time'],
                ]);
                $totalAmount += ($additional->good->additional_cost ?? $additional->good->cost) * $diffInDays;
            }
        }

        $totalAmount = $totalAmount / 100 * (100 - $client->discount);
        $order->amount_paid = $order->amount_paid + $totalAmount;

        $order->save();

        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');

        $order->save();

        Alert::info('You have successfully created a orderItem.');

        return redirect()->route('platform.orderItems.list', ["filter[order_id]" => $orderItem->order->id]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(OrderItem $orderItem)
    {
        $orderItem->order->amount_paid = $orderItem->order->amount_paid - $orderItem->amount_paid;

        $orderItem->order->save();

        if ($orderItem->is_additional){
            $parentOrderItem = OrderItem::find($orderItem->parent_order_item_id);

            $newAdditionalIds = $parentOrderItem->additionals;

            unset($newAdditionalIds[array_search($orderItem->item->id, $parentOrderItem->additionals)]);

            $parentOrderItem->additionals = $newAdditionalIds;
            $parentOrderItem->save();
        } else {
            $orderItem->order->orderItems()->whereIn('item_id', $orderItem->additionals ?? [])->delete();
        }

        $orderItem->delete();

        Alert::info('You have successfully deleted the orderItem.');

        $orderItem->order->rent_end_date = $orderItem->order->orderItems()->max('rent_end_date');
        $orderItem->order->rent_start_date = $orderItem->order->orderItems()->min('rent_start_date');

        $orderItem->order->save();


        return redirect()->route('platform.orderItems.list', ["filter[order_id]" => $orderItem->order->id]);
    }

    public function generateTimeSpans()
    {
        $arr = [];
        for ($hours = 0; $hours < 24; $hours++) {
            for ($minutes = 0; $minutes < 60; $minutes += 5) {
                $hoursStr = str_pad($hours, 2, '0', STR_PAD_LEFT);
                $minutesStr = str_pad($minutes, 2, '0', STR_PAD_LEFT);

                $arr["$hoursStr:$minutesStr:00"] = "$hoursStr:$minutesStr:00";
            }
        }

        return $arr;
    }
}
