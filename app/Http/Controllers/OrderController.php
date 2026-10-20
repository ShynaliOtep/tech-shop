<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wanted;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Good\GoodItemService;
use App\Services\Good\TimeRange;
use App\Services\Order\BookingLock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function confirmOrder()
    {
        return response(view('ordering.final'))->cookie('cart', '{}', 60 * 30 * 24);
    }

    public function spamGuard()
    {
        return response(view('_v2.pages.ordering.spamGuard'))->cookie('cart', '{}', 60 * 30 * 24);
    }

    public function settleOrderAll(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (Cache::get($client->id) !== null){
            return redirect(route('spamGuard'));
        }

        Cache::put($client->id, '{}', 15);

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            Auth::guard('clients')->logout();

            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }

        $requestData = $request->all();

        if ($request->cookie('cart', '{}') === '{}') {
            return redirect()->back()->withErrors(['cart' => 'Пожалуйста, выберите товары и оформите по ним заказ']);
        }

        $uniqueCartData = [];
        $cartData = json_decode($request->cookie('cart', '{}'), true);
        foreach ($cartData as $key => $value) {
            if (!array_key_exists($key, $uniqueCartData)) {
                $uniqueCartData[$key] = $value;
            }
        }
        $cartData = $uniqueCartData;

        // Экземпляры в старой корзине выбраны заранее и лежат в cookie — к моменту оформления
        // их могли занять. Проверяем и бронируем под блокировкой, иначе получаем двойную бронь.
        DB::beginTransaction();

        $busyNames = $this->busyItemNames($cartData, fn () => [
            Carbon::createFromFormat('d/m/Y H:i', $requestData['rent_start_date'].' '.$requestData['start_time']),
            Carbon::createFromFormat('d/m/Y H:i', $requestData['rent_end_date'].' '.$requestData['end_time']),
        ]);

        if ($busyNames) {
            DB::rollBack();
            Cache::forget($client->id);

            return redirect()->back()->withErrors(['cart' => 'На выбранные даты уже занято: '.implode(', ', $busyNames)]);
        }

        $totalSum = 0;

        $orderItemMessageData = '';

        $order = Order::query()->create([
            'client_id' => $client->id,
            'amount_paid' => 0,
            'status' => 'waiting',
        ]);

        foreach ($cartData as $itemKey => $itemValue) {
            $itemKeySeparated = explode('pixelrental', $itemKey);
            $goodId = $itemKeySeparated[0];
            $itemId = $itemKeySeparated[1];
            $good = Good::query()->find($goodId);

            $dateObj1 = DateTime::createFromFormat('d/m/Y H:i', $requestData['rent_start_date'].' '.$requestData['start_time']);
            $dateObj2 = DateTime::createFromFormat('d/m/Y H:i', $requestData['rent_end_date'].' '.$requestData['end_time']);

            $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

            $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

            $diffInDays = max(1, $diffInDays);

            $orderItemMessageData = $orderItemMessageData.'Товар: '.str_replace(")", "", str_replace("(", "", $good->name_ru)).'
';

            $clientPrice = $good->getPriceForClient($client);
            $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $clientPrice . ($clientPrice < $good->cost ? '(скидка)' : '') . "\n";

            $orderItemMessageData = $orderItemMessageData.'Дата начала аренды: *'.$dateObj1->format('d/m/Y H:i').'*
';
            $orderItemMessageData = $orderItemMessageData.'Дата конца аренды: *'.$dateObj2->format('d/m/Y H:i').'*
';
            $orderItemMessageData = $orderItemMessageData.'Количество дней: *'.$diffInDays.'*
';
            $currentItemCost = $diffInDays * $clientPrice;
            $orderItemMessageData = $orderItemMessageData.'Общая сумма за товар: *'.$currentItemCost.'*
';
            $orderItemMessageData = $orderItemMessageData.'Дополнения к товару:
';
            $parentOrderItem = OrderItem::query()->create([
                'item_id' => $itemId,
                'status' => 'waiting',
                'amount_of_days' => $diffInDays,
                'order_id' => $order->id,
                'is_additional' => false,
                'additionals' => $cartData[$itemKey] ?? [],
                'amount_paid' => $currentItemCost,
                'rent_start_date' => $dateObj1->format('Y-m-d'),
                'rent_start_time' => $dateObj1->format('H:i:s'),
                'rent_end_date' => $dateObj2->format('Y-m-d'),
                'rent_end_time' => $dateObj2->format('H:i:s'),
            ]);

            foreach ($cartData[$itemKey] as $additionalId) {
                $additional = Item::query()->find($additionalId)->load('good');

                $additionalCost = $additional->good->getAdditionalPriceForClient($client) * $diffInDays;

                $orderItemMessageData = $orderItemMessageData.'   Наименование: '.$additional->good->name_ru.'
';
                $orderItemMessageData = $orderItemMessageData.'       Цена: '.$additional->good->getAdditionalPriceForClient($client).'
';

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
                    'rent_start_date' => $dateObj1->format('Y-m-d'),
                    'rent_start_time' => $dateObj1->format('H:i:s'),
                    'rent_end_date' => $dateObj2->format('Y-m-d'),
                    'rent_end_time' => $dateObj2->format('H:i:s'),
                ]);
            }

            $totalSum += $currentItemCost;

        }

        $order->amount_paid = $totalSum;
        $order->save();

        DB::commit();

        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $order->id;

        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');

        $cityId = session()->get('select_city');
        $cityId = $cityId ?: City::DEFAULT;
        $order->city_id = $cityId;

        $order->save();

        $aggreementUrl = $aggreementFile->url();

        Log::info('settleOrder', [$client, $order]);

        $response = sendTelegramMessage(
            "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)".
//Скидка: $client->discount процентов
"Общая сумма: $totalSum тг

Список товаров:
".$orderItemMessageData);

        if (! $response->ok()) {
            sendTelegramMessage(
                "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)
Скидка: $client->discount процентов
Общая сумма: $totalSum тг

Список товаров слишком большой для отображения в боте.");
        }

        return redirect(route('confirmOrder'));
    }
    public function settleOrder(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (Cache::get($client->id) !== null){
            return redirect(route('spamGuard'));
        }

        Cache::put($client->id, '{}', 15);

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            Auth::guard('clients')->logout();

            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }

        $requestData = $request->all();

        if ($request->cookie('cart', '{}') === '{}') {
            return redirect()->back()->withErrors(['cart' => 'Пожалуйста, выберите товары и оформите по ним заказ']);
        }

        $uniqueCartData = [];
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        foreach ($cartData as $key => $value) {
            if (!array_key_exists($key, $uniqueCartData)) {
                $uniqueCartData[$key] = $value;
            }
        }
        $cartData = $uniqueCartData;

        // См. settleOrderAll: проверка и бронирование под одной блокировкой.
        DB::beginTransaction();

        $busyNames = $this->busyItemNames($cartData, fn ($itemKey) => [
            Carbon::createFromFormat('d/m/Y H:i', $requestData[$itemKey]['rent_start_date'].' '.$requestData[$itemKey]['start_time']),
            Carbon::createFromFormat('d/m/Y H:i', $requestData[$itemKey]['rent_end_date'].' '.$requestData[$itemKey]['end_time']),
        ]);

        if ($busyNames) {
            DB::rollBack();
            Cache::forget($client->id);

            return redirect()->back()->withErrors(['cart' => 'На выбранные даты уже занято: '.implode(', ', $busyNames)]);
        }

        $totalSum = 0;

        $orderItemMessageData = '';

        $order = Order::query()->create([
            'client_id' => $client->id,
            'amount_paid' => 0,
            'status' => 'waiting',
        ]);

        foreach ($cartData as $itemKey => $itemValue) {
            $itemKeySeparated = explode('pixelrental', $itemKey);
            $goodId = $itemKeySeparated[0];
            $itemId = $itemKeySeparated[1];
            $good = Good::query()->find($goodId);

            $requestParticularGood = $requestData[$itemKey];

            $dateObj1 = DateTime::createFromFormat('d/m/Y H:i', $requestParticularGood['rent_start_date'].' '.$requestParticularGood['start_time']);
            $dateObj2 = DateTime::createFromFormat('d/m/Y H:i', $requestParticularGood['rent_end_date'].' '.$requestParticularGood['end_time']);

            $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

            $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

            $diffInDays = max(1, $diffInDays);

            $orderItemMessageData = $orderItemMessageData.'Товар: '.str_replace(")", "", str_replace("(", "", $good->name_ru)).'
';

            $clientPrice = $good->getPriceForClient($client);
            $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $clientPrice . ($clientPrice < $good->cost ? '(скидка)' : '') . "\n";

            $orderItemMessageData = $orderItemMessageData.'Дата начала аренды: *'.$dateObj1->format('d/m/Y H:i').'*
';
            $orderItemMessageData = $orderItemMessageData.'Дата конца аренды: *'.$dateObj2->format('d/m/Y H:i').'*
';
            $orderItemMessageData = $orderItemMessageData.'Количество дней: *'.$diffInDays.'*
';
            $currentItemCost = $diffInDays * $clientPrice;
            $orderItemMessageData = $orderItemMessageData.'Общая сумма за товар: *'.$currentItemCost.'*
';
            $orderItemMessageData = $orderItemMessageData.'Дополнения к товару:
';
            $parentOrderItem = OrderItem::query()->create([
                'item_id' => $itemId,
                'status' => 'waiting',
                'amount_of_days' => $diffInDays,
                'order_id' => $order->id,
                'is_additional' => false,
                'additionals' => $cartData[$itemKey] ?? [],
                'amount_paid' => $currentItemCost,
                'rent_start_date' => $dateObj1->format('Y-m-d'),
                'rent_start_time' => $dateObj1->format('H:i:s'),
                'rent_end_date' => $dateObj2->format('Y-m-d'),
                'rent_end_time' => $dateObj2->format('H:i:s'),
            ]);

            foreach ($cartData[$itemKey] as $additionalId) {
                $additional = Item::query()->find($additionalId)->load('good');

                $additionalCost = $additional->good->getAdditionalPriceForClient($client) * $diffInDays;

                $orderItemMessageData = $orderItemMessageData.'   Наименование: '.$additional->good->name_ru.'
';
                $orderItemMessageData = $orderItemMessageData.'       Цена: '.$additional->good->getAdditionalPriceForClient($client).'
';

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
                    'rent_start_date' => $dateObj1->format('Y-m-d'),
                    'rent_start_time' => $dateObj1->format('H:i:s'),
                    'rent_end_date' => $dateObj2->format('Y-m-d'),
                    'rent_end_time' => $dateObj2->format('H:i:s'),
                ]);
            }

            $totalSum += $currentItemCost;

        }

        $order->amount_paid = $totalSum;

        $cityId = session()->get('select_city');
        $cityId = $cityId ?: City::DEFAULT;
        $order->city_id = $cityId;

        $order->save();

        DB::commit();

        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $order->id;

        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');

        $order->save();

        Log::info('settleOrder', [$client, $order]);

        $aggreementUrl = $aggreementFile->url();

        $response = sendTelegramMessage(
            "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)
Скидка: $client->discount процентов
Общая сумма: $totalSum тг

Список товаров:
".$orderItemMessageData);

        if (! $response->ok()) {
            sendTelegramMessage(
                "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)
Скидка: $client->discount процентов
Общая сумма: $totalSum тг

Список товаров слишком большой для отображения в боте.");
        }


        return redirect(route('confirmOrder'));
    }

    /**
     * Названия экземпляров из cookie-корзины, которые уже заняты на нужный период.
     * Вызывать внутри транзакции: перед проверкой экземпляры блокируются.
     *
     * @param callable $period fn(string $itemKey): array{0: Carbon, 1: Carbon}
     * @return string[]
     */
    private function busyItemNames(array $cartData, callable $period): array
    {
        $itemIdsByKey = [];
        foreach ($cartData as $itemKey => $additionalIds) {
            $itemIdsByKey[$itemKey] = array_merge(
                [explode('pixelrental', $itemKey)[1] ?? null],
                (array) $additionalIds,
            );
        }

        BookingLock::items(array_merge(...array_values($itemIdsByKey)));

        $service = new GoodItemService();
        $busy = [];

        foreach ($itemIdsByKey as $itemKey => $itemIds) {
            [$start, $end] = $period($itemKey);
            $timeRange = new TimeRange($start, $end);

            foreach ($itemIds as $itemId) {
                $item = Item::query()->with('good')->find($itemId);

                if ($item && ! $service->isItemAvailableByTime($timeRange, $item)) {
                    $busy[] = $item->good->name_ru ?? $item->name;
                }
            }
        }

        return array_values(array_unique($busy));
    }

    public function countDistinctKeys($array)
    {
        $counts = [];
        foreach ($array as $key => $value) {
            $goodId = explode('pixelrental', $key)[0];
            $counts[$goodId] = 0;
            foreach ($array as $subArrayKey => $subArrayValue) {
                if (explode('pixelrental', $subArrayKey)[0] === $goodId) {
                    $counts[$goodId] += 1;
                }
            }
        }

        return $counts;
    }
}
