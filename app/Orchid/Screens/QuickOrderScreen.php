<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Client;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Bonus\GoodService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class QuickOrderScreen extends Screen
{
    public $name = 'Быстрое оформление заказа';

    public $showPhoneField = false;

    // Храним состояние модалки (нужно для обновления интерфейса)
    public $showAddItemModal = false;

    public function commandBar(): array
    {
        return [
            Button::make('Оформить заказ')
                ->method('saveOrder'),
            // ✅ Открываем модалку через AJAX, без полной перезагрузки
            ModalToggle::make('Добавить товар')
                ->icon('plus')
                ->method('addItem')
                ->modal('addItemModal')

        ];
    }

    public function layout(): iterable
    {
        $itemOptions = Item::all()->pluck('name', 'id')->toArray();
        return [
            Layout::rows([
                Relation::make('order.client_id')
                    ->fromModel(Client::class, 'name')
                    ->help(__('translations.Order client help'))
                    ->required()
                    ->title(__('translations.Client'))
                    ->update('dynamicFields', 'asyncUpdateFields'),
                Input::make('order.phone')
                    ->title('Номер телефона')
                    ->mask('+7 (999) 999-99-99') // Формат маски номера
                    ->required()
                    ->canSee($this->showPhoneField),
                Select::make('order.status')
                    ->options([
                        'returned' => __('translations.returned'),
                        'in_rent' => __('translations.in_rent'),
                        'waiting' => __('translations.waiting'),
                        'confirmed' => __('translations.confirmed'),
                        'cancelled' => __('translations.cancelled'),
                    ])
                    ->title(__('translations.Status'))
                    ->help(__('translations.Order status help')),

                Select::make('order.paid_status')
                    ->options([
                        'pending' => __('Ожидается'),
                        'paid' => __('Оплачен'),
                        'unpaid' => __('Не оплачен'),
                    ])
                    ->title(__('Статус оплаты')),
                //  ->help(__('translations.Order status help')),

                Input::make('order.amount_unpaid')
                    ->title(__('Не оплаченная сумма'))
                    //  ->help(__('translations.Order agreement id help')),
                    ->type('number'),
            ]),

            Layout::legend('order_info', [
                Sight::make('total_cost', 'Итого')->render(fn () =>session()->get('total_cost', 0)),
            ]),

            Layout::table('selectedItems', [
                TD::make('item_id', 'ID')->render(fn($item) => $item['item_id']),
                TD::make('name', 'Товар')->render(fn($item) => $item['name']),
                TD::make('rent_start_date', 'Дата начала')->render(fn($item) => $item['rent_start_date']),
                TD::make('rent_start_time', 'Время начала')->render(fn($item) => $item['rent_start_time']),
                TD::make('rent_end_date', 'Дата конца')->render(fn($item) => $item['rent_end_date']),
                TD::make('rent_end_time', 'Время конца')->render(fn($item) => $item['rent_end_time']),
                TD::make('cost', 'Цена')->render(fn($item) => $item['cost']),
                TD::make('add_names', 'Дополнения к товарам')->render(fn($item) => $item['add_names']),
                TD::make('is_additional', 'Товар берут как дополнение?')->render(fn($item) => $item['is_additional'] ? 'Да' : 'Нет'),
                TD::make('actions', 'Действия')->render(fn($item) =>
                Button::make('Удалить')
                    ->method('removeItem', ['id' => $item['id']])
                ),
            ])->title('Выбранные товары'),

            // ✅ Делаем модалку асинхронной, чтобы не перезагружалась страница
            Layout::modal('addItemModal', [
                Layout::rows([
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
                    Select::make('orderItem.item_id')
                        ->options($itemOptions)
                        ->help(__('translations.OrderItem item help'))
                        ->required()
                        ->title(__('translations.Item')),
                    Select::make('orderItem.additionals')
                        ->options(
                            $itemOptions
                        )
                        ->multiple()
                        ->help(__('translations.OrderItem additional help'))
                        ->title(__('translations.Additionals')),
                    Select::make('orderItem.is_additional')
                        ->options([
                            false => 'Нет',
                            true => 'Да',
                        ])
                        ->title(__('translations.Is additional'))
                        ->required()
                        ->help(__('translations.OrderItem is_additional help')),
                ]),
            ])->title('Добавить товар')
                ->applyButton('Добавить')
                ->closeButton('Отмена')
                ->method('addItem')
               // ->async('asyncUpdateAvailableItems'),
        ];
    }

    public function query(): array
    {
        return [
            'selectedItems' => session()->get('quick_order_items', [])
        ];
    }

    public function asyncUpdateFields(Request $request): array
    {
        $clientId = $request->get('order.client_id');

        return [
            'showPhoneField' => $clientId === 1065, // Показываем поле, если выбран "Гость"
        ];
    }

    public function updatePhoneField(Request $request)
    {
        session(['showPhoneField' => $request->input('client') === 'guest']);
    }



//    public function query(Request $request): array
//    {
//        $startDate = $request->input('orderItem.rent_start_date');
//        $startTime = $request->input('orderItem.rent_start_time');
//        $endDate = $request->input('orderItem.rent_end_date');
//        $endTime = $request->input('orderItem.rent_end_time');
//        //dd($startDate, $startTime, $endDate, $endTime);
//        $items = [];
//        if ($startDate && $startTime && $endDate && $endTime) {
//            $items = (new GoodService())->getAllAvailableItems($startDate, $startTime, $endDate, $endTime);
//        }
//
//        return [
//            'rent_start_date' => $startDate,
//            'items' => $items,
//        ];
//    }

    public function getAvailableItems()
    {
        $startDate = request()->input('orderItem.rent_start_date');
        $startTime = request()->input('orderItem.rent_start_time');
        $endDate = request()->input('orderItem.rent_end_date');
        $endTime = request()->input('orderItem.rent_end_time');

        $items = [];
        if ($startDate && $startTime && $endDate && $endTime) {
            $items = (new GoodService())->getAllAvailableItems($startDate, $startTime, $endDate, $endTime);
        }

        return $items;
    }


// ✅ Метод, который теперь НЕ перезагружает страницу
    public function openAddItemModal()
    {
        return [];
    }

//    public function query(): iterable
//    {
//        return [
//            'selectedItems' => session()->get('quick_order_items', []),
//        ];
//    }

    // ✅ Добавление товара в список
    public function addItem(Request $request)
    {
        $data = $request->all();

        $item = Item::where('id', $data['orderItem']['item_id'])->first();



        $date1 = Carbon::parse( $data['orderItem']['rent_start_date']);
        $date2 = Carbon::parse($data['orderItem']['rent_end_date'],);

        $diffInDays = $date1->diffInDays($date2);

        $diffInDays = max($diffInDays, 1);

        $cost = $diffInDays * $item->good->cost;

        if (isset($data['orderItem']['additionals'])) {
            $addNames = [];
            foreach ($data['orderItem']['additionals'] as $additionalId) {
                $additional = Item::query()->find($additionalId)->load('good');
                $addNames[] = $additional->good->name_ru;
                $additionalCost = (($additional->good->additional_cost !== null && $additional->good->additional_cost > 0) ? $additional->good->additional_cost : $additional->good->cost) * $diffInDays;
                $cost += $additionalCost;
            }
        }


        $items = session()->get('quick_order_items', []);
        $items[] = [
            'id' => uniqid(),
            'rent_start_date' => $data['orderItem']['rent_start_date'],
            'rent_start_time' => $data['orderItem']['rent_start_time'],
            'rent_end_date' => $data['orderItem']['rent_end_date'],
            'rent_end_time' => $data['orderItem']['rent_end_time'],
            'item_id' => $data['orderItem']['item_id'],
            'additionals' => $data['orderItem']['additionals'] ?? null,
            'is_additional' => $data['orderItem']['is_additional'],
            'name' => $item->good->name_ru,
            'add_names' => isset($addNames) ? implode(', ', $addNames) : '',
            'cost' => $cost,
        ];

        session()->put('quick_order_items', $items);

        $totalCost = session()->get('total_cost', 0);
        $totalCost = $totalCost + $cost;
        session()->put('total_cost', $totalCost);

        Toast::info('Товар добавлен!');
    }

    // ✅ Удаление товара из списка
    public function removeItem(Request $request)
    {
        $items = session()->get('quick_order_items', []);

        foreach ($items as $item) {
            if ($item['id'] == $request->input('id')) {
                $totalCost = session()->get('total_cost', 0);
                $totalCost = $totalCost - $item['cost'];
                session()->put('total_cost', $totalCost);
                break;
            }
        }

        $items = array_filter($items, fn($item) => $item['id'] !== $request->input('id'));
        session()->put('quick_order_items', array_values($items));

        if (!$items || empty($items)) {
            session()->put('total_cost', 0);
        }

        Toast::info('Товар удалён!');
    }

    public function saveOrder(Request $request)
    {
        $order = Order::create($request->input('order'));
        $items1 = session()->get('quick_order_items', []);
        $totalSum = 0;

        if (isset($items['item_id'])) {
            $items[] = $items1;
        } else {
            $items = $items1;
        }

        foreach ($items as $item) {
            $item = Item::query()->find($item['item_id'])->load('good');

            $dateObj1 = Carbon::parse($item['rent_start_date'].' '.$item['rent_start_time']);
            $dateObj2 = Carbon::parse($item['rent_end_date'].' '.$item['rent_end_time']);

            $date1 = Carbon::parse($item['rent_start_date']);
            $date2 = Carbon::parse($item['rent_end_date']);

            $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

            $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

            $diffInDays = max(1, $diffInDays);

            $currentItemCost = $diffInDays * $item->good->cost;

            $parentOrderItem = OrderItem::query()->create([
                'item_id' => $item['id'],
                'status' => $order->status,
                'amount_of_days' => $diffInDays,
                'order_id' => $order->id,
                'is_additional' => $item['is_additional'] ? 1 : 0,
                'additionals' => $item['additionals'] ?? [],
                'amount_paid' => $currentItemCost,
                'rent_start_date' => $dateObj1->format('Y-m-d'),
                'rent_start_time' => $dateObj1->format('H:i:s'),
                'rent_end_date' => $dateObj2->format('Y-m-d'),
                'rent_end_time' => $dateObj2->format('H:i:s'),
            ]);

//            foreach ($item['additionals'] as $additionalId) {
//                $additional = Item::query()->find($additionalId)->load('good');
//
//                $additionalCost = (($additional->good->additional_cost !== null && $additional->good->additional_cost > 0) ? $additional->good->additional_cost : $additional->good->cost) * $diffInDays;
//
//                $totalSum += $additionalCost;
//
//                OrderItem::query()->create([
//                    'item_id' => $additionalId,
//                    'order_id' => $order->id,
//                    'parent_order_item_id' => $parentOrderItem->id,
//                    'status' => 'waiting',
//                    'amount_of_days' => $diffInDays,
//                    'is_additional' => true,
//                    'additionals' => [],
//                    'amount_paid' => $additionalCost,
//                    'rent_start_date' => $item['rent_start_date'],
//                    'rent_start_time' =>  $item['rent_start_date'],
//                    'rent_end_date' =>  $item['rent_start_date'],
//                    'rent_end_time' =>  $item['rent_start_date'],
//                ]);
//            }

            $totalSum += $currentItemCost;
        }

        $order->amount_paid = $totalSum;
        $order->save();

        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $order->id;

        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');

        $cityId = session()->get('selected_city');
        $cityId = $cityId ?: City::DEFAULT;
        $order->city_id = $cityId;
        $order->save();

        session()->forget('quick_order_items');
        session()->forget('total_cost');
        Toast::success('Заказ успешно оформлен!');
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
