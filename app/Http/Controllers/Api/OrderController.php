<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wanted;
use App\Services\Bonus\GoodService;
use App\Services\Good\GoodItemService;
use App\Services\Good\TimeRange;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (Cache::get($client->id) !== null){
            return response()->json([
                'success' => false,
                'status' => 'spamGuard',
            ]);
        }


        Cache::put($client->id, '{}', 15);

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

        $totalSum = 0;

        $orderItemMessageData = '';

        $order = Order::query()->create([
            'client_id' => $client->id,
            'amount_paid' => 0,
            'status' => 'waiting',
        ]);

        $goodItemService = new GoodItemService;

        foreach ($requestData['cart'] as $item) {
            $date = $item['date'];
            $goodId = $item['id'];

            $dateObj1 = Carbon::parse($date['dateStart'] . ' ' . $date['timeStart'] . ':00');
            $dateObj2 = Carbon::parse($date['dateEnd'] . ' ' . $date['timeEnd'] . ':00');
            try {

                $availableItems = $goodItemService->getAvailableGoodItems(
                    new TimeRange(
                        $dateObj1,
                        $dateObj2
                    ),
                    $goodId,
                    $item['quantity'],
                );

                $requestAdditionsData = $item['additions'];
                $itemAdditionsData = [];
                foreach ($requestAdditionsData as $itemAdditional) {
                    $itemAdditionsData[] = $goodItemService->getAvailableGoodItems(
                        new TimeRange(
                            $dateObj1,
                            $dateObj2
                        ),
                        $itemAdditional['goodId'],
                        $itemAdditional['quantity'],
                    );
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'status' => 'notAvailable',
                    'message' => $e->getMessage()
                ]);
            }

            foreach ($itemAdditionsData as $itemAdditionData) {
                foreach ($itemAdditionData as $index => $itemAddition) {
                    if (count($availableItems) > $index) {
                        $array =  $availableItems[$index]->additionalItems;
                        $array[] = $itemAddition;
                        $availableItems[$index]->additionalItems = $array;
                    } else  {
                        $lastIndex = count($availableItems) - 1;
                        $array =  $availableItems[$lastIndex]->additionalItems;
                        $array[] = $itemAddition;
                        $availableItems[$lastIndex]->additionalItems = $array;
                    }
                }
            }


            $itemCount = 1;
            foreach ($availableItems as $itemObject) {

                $itemId = $itemObject->id;

                $good = Good::query()->find($goodId);

                $diffInSeconds = $dateObj2->getTimestamp() - $dateObj1->getTimestamp();

                $diffInDays = ceil($diffInSeconds / (60 * 60 * 24));

                $diffInDays = max(1, $diffInDays);

                $orderItemMessageData = $orderItemMessageData . 'Товар: ' . str_replace(")", "", str_replace("(", "", $good->name_ru)) . '
                ';


                if ($good->discount_cost) {
                    $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $good->discount_cost . '(скидка)
                ';
                } else {
                    $orderItemMessageData = $orderItemMessageData . 'Цена: ' . $good->cost . '
                ';
                }

                $orderItemMessageData = $orderItemMessageData . 'Дата начала аренды: *' . $dateObj1->format('d/m/Y H:i') . '*
                ';
                $orderItemMessageData = $orderItemMessageData . 'Дата конца аренды: *' . $dateObj2->format('d/m/Y H:i') . '*
                ';
                $orderItemMessageData = $orderItemMessageData . 'Количество дней: *' . $diffInDays . '*
                ';

                $currentItemCost = $diffInDays * ($good->discount_cost ?? $good->cost);
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
                    'additionals' =>  $additionsIds ?? [],
                    'amount_paid' => $currentItemCost,
                    'rent_start_date' => $dateObj1->format('Y-m-d'),
                    'rent_start_time' => $dateObj1->format('H:i:s'),
                    'rent_end_date' => $dateObj2->format('Y-m-d'),
                    'rent_end_time' => $dateObj2->format('H:i:s'),
                ]);

                if ($itemObject->additionalItems) {
                    foreach ($itemObject->additionalItems as $additionalItem) {

                        $additionalId = $additionalItem->id;

                        $additional = Item::query()->find($additionalId)->load('good');

                        $orderItemMessageData = $orderItemMessageData . '   Наименование: ' . $additional->good->name_ru . '
                    ';

                        $orderItemMessageData = $orderItemMessageData . '       Цена: ' . (($additional->good->additional_cost !== null && $additional->good->additional_cost > 0) ? $additional->good->additional_cost : $additional->good->cost) . '
                    ';

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
                            'amount_paid' => $additionalCost / 100 * (100 - $client->discount),
                            'rent_start_date' => $dateObj1->format('Y-m-d'),
                            'rent_start_time' => $dateObj1->format('H:i:s'),
                            'rent_end_date' => $dateObj2->format('Y-m-d'),
                            'rent_end_time' => $dateObj2->format('H:i:s'),
                        ]);
                    }
                }

                $totalSum += $currentItemCost;

                $itemCount++;
             }
        }

        if ($client->discount) {
            $totalSum = $totalSum / 100 * (100 - $client->discount);
        }

        $order->amount_paid = $totalSum;
        $order->save();

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

        return response()->json([
            'success' => true
        ]);
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
}
