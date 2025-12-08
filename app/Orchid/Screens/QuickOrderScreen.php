<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Bonus\GoodService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Label;
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

    public $rentStartDate;
    public $requestStartTime;
    public $requstEntDate;
    public $requestEndTime;

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

       // session()->forget('quick_order_items');
        if ($this->query()['client_type'] == 'client') {
            $forms[] = Label::make('order.client_id')
                ->title(__('translations.Client'))
                ->value(Client::find($this->query()['client_id'])->name);
            $forms[] = Input::make('order.client_id')
                ->value($this->query()['client_id'])
                ->style()
                ->hidden();
        } else {
            $forms[] = Label::make('order.phone')
                ->title('Номер телефона')
                ->value($this->query()['phone']);
            $forms[] = Input::make('order.phone')
                ->value($this->query()['phone'])
                ->hidden();
            $forms[] = Label::make('order.instagram')
                ->title('Инстаграм')
                ->value($this->query()['instagram']);
            $forms[] = Input::make('order.instagram')
                ->value($this->query()['instagram'])
                ->hidden();
        }
        $statuses = [
            'pending' => __('Ожидается'),
            'paid' => __('Оплачен'),
            'unpaid' => __('Не оплачен'),
        ];
        $paidStatus =  $statuses[$this->query()['paid_status']];

        return [
            Layout::rows(array_merge( $forms , [
                    Label::make('order.status')
                        ->title(__('translations.Status'))
                        ->value( __('translations.' . $this->query()['status'])),
                    Label::make('order.paid_status')
                        ->title(__('Статус оплаты'))
                        ->value($paidStatus),
                    Label::make('order.amount_unpaid')
                       ->title(__('Неоплаченная сумма'))
                       ->value($this->query()['amount_unpaid']),
                     Input::make('order.status')
                        ->value($this->query()['status'])
                        ->hidden(),
                    Input::make('order.paid_status')
                        ->value($this->query()['paid_status'])
                        ->hidden(),
                    Input::make('order.amount_unpaid')
                        ->value($this->query()['amount_unpaid'])
                        ->hidden(),
                    Input::make('order.client_type')
                        ->value($this->query()['client_type'])
                        ->hidden()
                ])
            ),

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
                        ->async()
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
                        ->options($this->getOptions())
                        ->help(__('translations.OrderItem item help'))
                        ->required()
                        ->title(__('translations.Item'))
                        ->async('loadOptions'),
                    Select::make('orderItem.additionals')
                        ->options($this->getOptions())
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
              //  ItemSelectListener::class
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
            'client_type' => request()->get('client_type'),
            'client_id' => request()->get('client_id'),
            'phone' => request()->get('phone'),
            'instagram' => request()->get('instagram'),
            'status' => request()->get('status'),
            'paid_status' => request()->get('paid_status'),
            'amount_unpaid' => request()->get('amount_unpaid'),
            'selectedItems' => session()->get('quick_order_items', []),
        ];
    }

    public function asyncGetOptions($rent_start_date, $rent_start_time, $rent_end_date, $rent_end_time)
    {
        return [
            'rent_start_date' => $rent_start_date,
            'rent_start_time' => $rent_start_time,
            'rent_end_date' => $rent_end_date,
            'rent_end_time' => $rent_end_time,
        ];
    }

    public function getOptions(): array
    {
        $cityId = City::getPlatformCity();
        $itemOptions = Good::whereHas('items', function ($query) use ($cityId) {
            $query->where('status', 'available')
                ->where('city_id', $cityId);
        })
            ->pluck('name_ru', 'id')
            ->toArray();
        return $itemOptions;
    }

    public function asyncData(Request $request): array
    {
        return [
            'rentStartDate' => $request->input('rent_start_date'),
            'rentStartTime' => $request->input('rent_start_time'),
            'rentEndDate' => $request->input('rent_end_date'),
            'rentEndTime' => $request->input('rent_end_time'),
        ];
    }

    public function asyncUpdateFields(Request $request): array
    {
        $clientId = $request->get('clint_type');

        return [
            'showPhoneField' => $clientId === 1065, // Показываем поле, если выбран "Гость"
        ];
    }

    public function updatePhoneField(Request $request)
    {
        session(['showPhoneField' => $request->input('client') === 'guest']);
    }

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

    // ✅ Добавление товара в список
    public function addItem(Request $request)
    {
        $data = $request->all();

        $good = Good::query()->find($data['orderItem']['item_id']);

        $itemId = $this->getAvailableItemsByTime(
            $data['orderItem']['item_id'],
            $data['orderItem']['rent_start_date'],
            $data['orderItem']['rent_start_time'],
            $data['orderItem']['rent_end_date'],
            $data['orderItem']['rent_end_time'],
        );

        if (!$itemId) {
            Toast::error('Товар недоступен на это время!');
            return;
        }

        /**
         * @var Item $item
         */
        $item = Item::where('id', $itemId)->first();



        $date1 = Carbon::parse( $data['orderItem']['rent_start_date']);
        $date2 = Carbon::parse($data['orderItem']['rent_end_date'],);

        $diffInDays = $date1->diffInDays($date2);

        $diffInDays = max($diffInDays, 1);

        $cost = $diffInDays * ($item->good->getDiscountCost('platform') ?? $item->good->cost);

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
            'item_id' => $itemId,
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

        $requestOrderData = $request->input('order');
        if ($requestOrderData['client_type'] == 'client') {
            $clientId = $requestOrderData['client_id'];
        } else {
            $id = rand(1000000, 10000000);
            $client = Client::query()->create([
                'name' => 'Guest' . $id,
                'phone' => $requestOrderData['phone'],
                'instagram' => $requestOrderData['instagram'],
                'discount' => 0,
                'email' => 'guest' . $id . '@mail.com',
                'iin' => $id,
                'confirmation_code' => '1111',
                'email_confirmed' => 1,
                'blocked' => 0,
                'password' => bcrypt($id),
            ]);
            $clientId = $client->id;
        }

        $order = Order::create([
            'status' => $requestOrderData['status'],
            'paid_status' => $requestOrderData['paid_status'],
            'amount_unpaid' => $requestOrderData['amount_unpaid'],
            'client_id' => $clientId,
        ]);

        $items1 = session()->get('quick_order_items', []);
        $totalSum = 0;

        if (isset($items['item_id'])) {
            $items[] = $items1;
        } else {
            $items = $items1;
        }

        //dd($items);

        foreach ($items as $item) {
            $itemObj = Item::query()->find($item['item_id'])->load('good');

            $dateObj1 = Carbon::parse($item['rent_start_date'].' '.$item['rent_start_time']);
            $dateObj2 = Carbon::parse($item['rent_end_date'].' '.$item['rent_end_time']);

           // dump($dateObj1, $dateObj2);

            $date1 = Carbon::parse($item['rent_start_date']);
            $date2 = Carbon::parse($item['rent_end_date']);

            $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

            $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

            $diffInDays = max(1, $diffInDays);
            /**
             * @var Good $good
             */
            $good = $itemObj->good;
            $currentItemCost = $diffInDays * ($good->getDiscountCost('platform') ?? $good->cost);

            $parentOrderItem = OrderItem::query()->create([
                'item_id' => $itemObj->id,
                'status' => $order->status,
                'amount_of_days' => $diffInDays,
                'order_id' => $order->id,
                'is_additional' => $item['is_additional'] ? 1 : 0,
                'additionals' => $item['additionals'] ?? [],
                'amount_paid' => $currentItemCost,
                'rent_start_date' => $item['rent_start_date'],
                'rent_start_time' => $item['rent_start_time'],
                'rent_end_date' => $item['rent_end_date'],
                'rent_end_time' => $item['rent_end_time'],
            ]);
            if ($item['additionals']) {
                foreach ($item['additionals'] as $additionalId) {
                    $additional = Item::query()->find($additionalId)->load('good');

                    $additionalCost = (($additional->good->additional_cost !== null && $additional->good->additional_cost > 0) ? $additional->good->additional_cost : $additional->good->cost) * $diffInDays;

                    $totalSum += $additionalCost;

                    OrderItem::query()->create([
                        'item_id' => $additionalId,
                        'order_id' => $order->id,
                        'parent_order_item_id' => $parentOrderItem->id,
                        'status' => 'waiting',
                        'amount_of_days' => $diffInDays,
                        'is_additional' => true,
                        'additionals' => [],
                        'amount_paid' => $additionalCost,
                        'rent_start_date' => $item['rent_start_date'],
                        'rent_start_time' =>  $item['rent_start_date'],
                        'rent_end_date' =>  $item['rent_start_date'],
                        'rent_end_time' =>  $item['rent_start_date'],
                    ]);
                }
            }

            $totalSum += $currentItemCost;
        }
        //dd('end');
        $order->amount_paid = $totalSum;
        $order->save();

        if ($requestOrderData['client_type'] == 'client') {
            $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

            $order->attachment()->syncWithoutDetaching($aggreementFile->id);
            $order->agreement_id = $order->id;
        }

        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');

        $cityId = session()->get('selected_city');
        $cityId = $cityId ?: City::DEFAULT;
        $order->city_id = $cityId;
        $order->manager_id = Auth::user()->id;
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

    public function getAvailableItemsByTime(
        int $id,
            $startDate,
            $startTime,
            $endDate,
            $endTime,
    )
    {
        $good = Good::query()->find($id);
        $cityId = City::getPlatformCity();

        $conflictingItemIds = DB::select("
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.good_id = :good_id
    AND items.city_id = :city_id
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < :end_date OR (order_items.rent_start_date = :end_date_v2 AND order_items.rent_start_time <= :end_time))
        AND
        (order_items.rent_end_date > :start_date OR (order_items.rent_end_date = :start_date_v2 AND order_items.rent_end_time >= :start_time))
    )
", [
            'good_id' => $good->id,
            'start_date' => $startDate,
            'start_date_v2' => $startDate,
            'start_time' => $startTime,
            'end_date' => $endDate,
            'end_date_v2' => $endDate,
            'end_time' => $endTime,
            'city_id' => $cityId,
        ]);
        $conflictingItemIds = array_map(function ($item) {
            return $item->item_id;
        }, $conflictingItemIds);

        $items = $good->items()->whereNotIn('id', $conflictingItemIds)->with('good')->get();

        foreach ($items as $item){
            $item->good->name = $item->good['name_'.session()->get('locale', 'ru')];
        }

        if ($items) {
            foreach ($items as $item){
                return $item->id;
            }
        }

        return null;
    }
}
