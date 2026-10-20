<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Client;
use App\Models\Delivery;
use App\Models\Good;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Set;
use App\Models\Wanted;
use App\Services\Bonus\GoodService;
use App\Services\Good\GoodItemService;
use App\Services\Good\TimeRange;
use App\Services\City\CityService;
use App\Services\Order\BookingLock;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (! $client) {
            return response()->json([
                'success' => false,
                'status' => 'authentication',
                'message' => 'Войдите в аккаунт, чтобы оформить заказ',
            ]);
        }

        $spamGuardKey = 'order-spam-guard:' . $client->id;

        if (Cache::get($spamGuardKey) !== null){
            return response()->json([
                'success' => false,
                'status' => 'spamGuard',
            ]);
        }

        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.date.dateStart' => 'required|date_format:Y-m-d',
            'cart.*.date.timeStart' => 'required|date_format:H:i',
            'cart.*.date.dateEnd' => 'required|date_format:Y-m-d',
            'cart.*.date.timeEnd' => 'required|date_format:H:i',
            'cart.*.additions' => 'present|array',
            'cart.*.additions.*.goodId' => 'required|integer',
            'cart.*.additions.*.quantity' => 'required|integer|min:0',
            'is_delivery' => 'required|boolean',
            'delivery.street' => 'required_if:is_delivery,true|nullable|string|max:255',
            'delivery.house' => 'required_if:is_delivery,true|nullable|string|max:50',
        ]);

        Cache::put($spamGuardKey, '{}', 15);

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            Auth::guard('clients')->logout();

            return response()->json([
                'success' => false,
                'status' => 'authentication',
                'message' => 'Профиль был заблокирован'
            ]);
        }

        $requestData = $request->all();

        Log::info('Order client:' . $client->id . ' data - ' . json_encode($requestData));

        $cart = [];
        foreach ($requestData['cart'] as $index => $item) {
            $set = Set::query()->where('good_id', $item['id'])->first();
            if ($set) {
                $cost = $set->good->getPriceForClient($client);
                foreach ($set->goods as $good) {
                    $item['id'] = $good->id;
                    $item['set_good_id'] = $set->good_id;
                    $item['set_good_name'] = $set->good->name_ru;
                    $item['cost'] = $cost;
                    $cost = 0;
                    $cart[] = $item;
                }
            } else {
                $cart[] = $item;
            }
        }

        $requestData['cart'] = $cart;

        // Нулевое количество дополнения означает «снят флажок» — бронировать нечего.
        foreach ($requestData['cart'] as &$item) {
            $item['additions'] = array_values(array_filter(
                $item['additions'] ?? [],
                fn ($addition) => (int) $addition['quantity'] > 0,
            ));
        }
        unset($item);

        foreach ($requestData['cart'] as $item) {
            $start = Carbon::parse($item['date']['dateStart'] . ' ' . $item['date']['timeStart']);
            $end = Carbon::parse($item['date']['dateEnd'] . ' ' . $item['date']['timeEnd']);

            if ($end <= $start) {
                Cache::forget($spamGuardKey);

                return response()->json([
                    'success' => false,
                    'status' => 'notAvailable',
                    'message' => 'Конец аренды должен быть позже начала',
                ]);
            }
        }

        $lockGoodIds = [];
        foreach ($requestData['cart'] as $item) {
            $lockGoodIds[] = $item['id'];
            foreach ($item['additions'] as $addition) {
                $lockGoodIds[] = $addition['goodId'];
            }
        }

        try {
            [$order, $totalSum, $orderItemMessageData, $delivery] = DB::transaction(
                fn () => $this->createOrder($client, $requestData, $lockGoodIds),
            );
        } catch (\Throwable $e) {
            Cache::forget($spamGuardKey);
            Log::warning('Order not created for client ' . $client->id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 'notAvailable',
                'message' => $e->getMessage()
            ]);
        }

        // Договор и уведомление — уже после коммита: брони не должны держать блокировку
        // экземпляров, пока генерируется PDF и идёт запрос в Telegram.
        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $order->id;
        $order->save();

        $aggreementUrl = $aggreementFile->url();

        Log::info('settleOrder', [$client, ['order' => $order->convertArray()]]);

        $deliveryText = '';
        if ($order->delivery) {
            $deliveryText = "🚚 Доставка:\n";
            $deliveryText .= "Улица: {$delivery->street}\n";
            $deliveryText .= "Дом: {$delivery->house}\n";

            if (!empty($delivery->apartment)) {
                $deliveryText .= "Квартира: {$delivery->apartment}\n";
            }

            if (!empty($delivery->entrance)) {
                $deliveryText .= "Подъезд: {$delivery->entrance}\n";
            }

            if (!empty($delivery->floor)) {
                $deliveryText .= "Этаж: {$delivery->floor}\n";
            }

            if (!empty($delivery->comment)) {
                $deliveryText .= "Комментарий: {$delivery->comment}\n";
            }
        }

        $response = sendTelegramMessage(
            "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)" .
//Скидка: $client->discount процентов
"Общая сумма: $totalSum тг
$deliveryText
Список товаров:
".$orderItemMessageData, $order->city_id);

        if (! $response->ok()) {
            sendTelegramMessage(
                "*НОВЫЙ ЗАКАЗ* $order->id
Покупатель: [$client->phone](https://wa.me/$client->phone)
Имя: $client->name
Электронный адрес: $client->email
Ссылка на договор: $aggreementUrl
ИИН: $client->iin
Инстаграм: [$client->instagram](https://www.instagram.com/$client->instagram/)" .
//Скидка: $client->discount процентов
"Общая сумма: $totalSum тг
$deliveryText

Список товаров слишком большой для отображения в боте.", $order->city_id);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Создаёт заказ с позициями. Вызывается внутри DB::transaction: любое исключение
     * (в том числе «не хватает товара») откатывает и сам заказ, и уже созданные позиции.
     *
     * @return array{0: Order, 1: int|float, 2: string, 3: Delivery|null}
     */
    private function createOrder(Client $client, array $requestData, array $lockGoodIds): array
    {
        // Блокировка до первой проверки доступности — см. BookingLock.
        BookingLock::goods($lockGoodIds);

        $totalSum = 0;
        $orderItemMessageData = '';
        $delivery = null;

        $order = Order::query()->create([
            'client_id' => $client->id,
            'amount_paid' => 0,
            'status' => 'waiting',
            'is_delivery' => (bool) $requestData['is_delivery'],
        ]);

        if ($requestData['is_delivery']) {
            $deliveryData = $requestData['delivery'];
            $delivery = new Delivery();
            $delivery->order_id = $order->id;
            $delivery->street = $deliveryData['street'];
            $delivery->house = $deliveryData['house'];
            $delivery->apartment = $deliveryData['apartment'] ?? null;
            $delivery->entrance = $deliveryData['entrance'] ?? null;
            $delivery->floor = $deliveryData['floor'] ?? null;
            $delivery->comment = $deliveryData['comment'] ?? null;
            $delivery->save();
        }

        $goodItemService = new GoodItemService;

        // Экземпляры, уже взятые этим же заказом. В базе брони появляются по ходу цикла,
        // но один и тот же товар может прийти и основной позицией, и дополнением —
        // второй раз подбирать тот же экземпляр нельзя.
        $takenItemIds = [];

        foreach ($requestData['cart'] as $item) {
            $date = $item['date'];
            $goodId = $item['id'];

            $dateObj1 = Carbon::parse($date['dateStart'] . ' ' . $date['timeStart'] . ':00');
            $dateObj2 = Carbon::parse($date['dateEnd'] . ' ' . $date['timeEnd'] . ':00');
            $timeRange = new TimeRange($dateObj1, $dateObj2);

            $availableItems = $this->pickItems($goodItemService, $timeRange, $goodId, $item['quantity'], $takenItemIds);

            foreach ($item['additions'] as $itemAdditional) {
                $additionItems = $this->pickItems(
                    $goodItemService,
                    $timeRange,
                    $itemAdditional['goodId'],
                    $itemAdditional['quantity'],
                    $takenItemIds,
                );

                foreach ($additionItems as $index => $itemAddition) {
                    $targetIndex = min($index, count($availableItems) - 1);
                    $array = $availableItems[$targetIndex]->additionalItems;
                    $array[] = $itemAddition;
                    $availableItems[$targetIndex]->additionalItems = $array;
                }
            }

            /**
             * @var Good $good
             */
            $good = Good::query()->find($goodId);

            $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();
            $diffInDays = max(1, ceil($diffInSeconds / (60 * 60 * 24)));

            foreach ($availableItems as $itemObject) {
                $itemId = $itemObject->id;

                $orderItemMessageData = $orderItemMessageData . 'Товар: ' . str_replace(")", "", str_replace("(", "", $good->name_ru)) . '
                ';

                if (array_key_exists('cost', $item)) {
                    $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $item['cost'] . '(из набора  ' . $item['set_good_name'] . ')
                ';
                } else {
                    $clientPrice = $good->getPriceForClient($client);
                    $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $clientPrice . ($clientPrice < $good->cost ? '(скидка)' : '') . '
                ';
                }

                $orderItemMessageData = $orderItemMessageData . 'Дата начала аренды: *' . $dateObj1->format('d/m/Y H:i') . '*
                ';
                $orderItemMessageData = $orderItemMessageData . 'Дата конца аренды: *' . $dateObj2->format('d/m/Y H:i') . '*
                ';
                $orderItemMessageData = $orderItemMessageData . 'Количество дней: *' . $diffInDays . '*
                ';

                $currentItemCost = $diffInDays * ($item['cost'] ?? $good->getPriceForClient($client));
                $orderItemMessageData = $orderItemMessageData . 'Общая сумма за товар: *' . $currentItemCost . '*
                ';

                $orderItemMessageData = $orderItemMessageData . 'Дополнения к товару:
                ';

                $additionsIds = [];
                if ($itemObject->additionalItems) {
                    foreach ($itemObject->additionalItems as $additionalItem) {
                        $additionsIds[] = $additionalItem->id;
                    }
                }

                $parentOrderItem = OrderItem::query()->create([
                    'item_id' => $itemId,
                    'status' => 'waiting',
                    'amount_of_days' => $diffInDays,
                    'order_id' => $order->id,
                    'is_additional' => false,
                    'additionals' => $additionsIds,
                    'amount_paid' => $currentItemCost,
                    'rent_start_date' => $dateObj1->format('Y-m-d'),
                    'rent_start_time' => $dateObj1->format('H:i:s'),
                    'rent_end_date' => $dateObj2->format('Y-m-d'),
                    'rent_end_time' => $dateObj2->format('H:i:s'),
                    'set_good_id' => $item['set_good_id'] ?? null,
                ]);

                if ($itemObject->additionalItems) {
                    foreach ($itemObject->additionalItems as $additionalItem) {
                        $additionalId = $additionalItem->id;

                        $additional = Item::query()->find($additionalId)->load('good');

                        $orderItemMessageData = $orderItemMessageData . '   Наименование: ' . $additional->good->name_ru . '
                    ';

                        $additionalCost = $additional->good->getAdditionalPriceForClient($client) * $diffInDays;

                        $orderItemMessageData = $orderItemMessageData . '       Цена: ' . $additional->good->getAdditionalPriceForClient($client) . '
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
                }

                $totalSum += $currentItemCost;
            }
        }

        $order->amount_paid = $totalSum;
        $order->rent_end_date = $order->orderItems()->max('rent_end_date');
        $order->rent_start_date = $order->orderItems()->min('rent_start_date');
        $order->city_id = CityService::city();
        $order->save();

        return [$order, $totalSum, $orderItemMessageData, $delivery];
    }

    /**
     * Свободные экземпляры товара на период, кроме уже взятых этим заказом.
     * Взятые добавляются в $takenItemIds.
     *
     * @return Item[]
     */
    private function pickItems(GoodItemService $service, TimeRange $timeRange, int $goodId, int $quantity, array &$takenItemIds): array
    {
        $free = array_values(array_filter(
            $service->getFreeItems($timeRange, $goodId),
            fn (Item $item) => ! in_array($item->id, $takenItemIds),
        ));

        if (count($free) < $quantity) {
            $good = Good::query()->find($goodId);
            throw new \RuntimeException(
                'Не хватает товара «' . ($good->name_ru ?? $goodId) . '» на выбранные даты: свободно ' . count($free) . ' из ' . $quantity
            );
        }

        $picked = array_slice($free, 0, $quantity);

        foreach ($picked as $pickedItem) {
            $takenItemIds[] = $pickedItem->id;
        }

        return $picked;
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

        $conflictingItemIds = DB::select("
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.good_id = :good_id
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

    public function getAgreement(int $orderId)
    {
        $order = Order::query()->find($orderId);

        if (! $order) {
            abort(404);
        }

        $attachment = $order->attachment()->first();

        if (!$attachment) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'attachment' => $attachment,
        ]);
    }

    public function saveSignature(Request $request)
    {
        $order = Order::find($request->order_id);
        // signature: "data:image/png;base64,iVBORw0KGgoAAAANS..."
        $signatureData = $request->input('signature');

        // Отрезаем префикс "data:image/png;base64,"
        $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $signatureData);

        // Декодируем
        $decoded = base64_decode($signatureData);

        if ($decoded === false) {
            return response()->json(['error' => 'Invalid base64 string'], 400);
        }

        // Генерируем имя файла
        $fileName = 'signatures/' . uniqid() . '.png';

        // Сохраняем файл в storage/app/public/signatures
        Storage::disk('public')->put($fileName, $decoded);

        // Сохраняем путь в БД
        $order->signature_url = $fileName;
        $order->save();

        foreach ($order->attachment as $attachment) {
            $attachment->delete();
        }

        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $aggreementFile->id;

        $order->save();

        return response()->json([
            'message' => 'Signature saved successfully',
            'path' => Storage::url($fileName), // для фронта сразу URL
            'agreement_url' => $order->attachment()->first()->url
        ]);
    }
}
