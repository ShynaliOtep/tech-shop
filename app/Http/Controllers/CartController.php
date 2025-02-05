<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request): JsonResponse
    {
        $goodId = $request->input('product_id');
        $additionalIds = $request->input('additional_ids', []);
        $cartData = json_decode($request->cookie('cart', '{}'), true);
        $idCounts = $this->countDistinctKeys($cartData);
        $good = Good::query()->find($goodId);

        if (! isset($idCounts[$goodId]) || $idCounts[$goodId] < count($good->items()->get())) {
            $itemId = $good->items()->offset($idCounts[$goodId] ?? 0)->first()->id;
            $cartData[$goodId.'pixelrental'.$itemId] = $additionalIds;

            return response()
                ->json(['success' => true])
                ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
        }

        return response()->json(['error' => 'На данный момент такой товар имеется в количестве: '.
            count(Good::query()->find($goodId)->items).'<br> Обратитесь к менеджеру для уточнения нужного количества.'], 400);
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);
        $goodIdToRemove = $request->input('product_id');
        unset($cartData[$goodIdToRemove]);

        return response()
            ->json(['success' => true])
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function changeCartKey(Request $request): JsonResponse
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);
        $keyToRemove = $request->input('key_to_remove');
        unset($cartData[$keyToRemove]);

        $cartData[$request->input('key_to_set')] = [];

        return response()
            ->json(['success' => true])
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function getCartCount(Request $request): JsonResponse
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);
        $cartCount = count($cartData);

        return response()->json(['cartCount' => $cartCount]);
    }

    public function cleanupCart(): JsonResponse
    {
        return response()->json(['success' => true])
            ->cookie('cart', json_encode([]), 60 * 24 * 30);
    }

    public function cart(Request $request)
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        foreach ($cartData as $key => $value) {
            $cartData[$key] = [];
        }

        if (Auth::guard('clients')->check()) {
            $client = Client::query()->find(Auth::guard('clients')->id())->toArray();
        } else {
            $client = null;
        }

        $itemIds = [];

        forEach($cartData as $key => $value){
            $itemIds[] = explode('pixelrental', $key)[1];
        }

        $items = Item::query()->whereIn('id', $itemIds)->get();
        return response(view('cart', compact('items', 'cartData', 'client')))->cookie('cart', json_encode($cartData), 60 * 24 * 30);
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

    public function getAvailableAdditionals(Request $request)
    {
        $startDateString = $request->input('startDate');
        $startTimeString = $request->input('startTime');
        $endDateString = $request->input('endDate');
        $endTimeString = $request->input('endTime');
        $startDateTimeString = $startDateString.' '.$startTimeString;
        $endDateTimeString = $endDateString.' '.$endTimeString;
        $goodId = $request->input('goodId');
        $good = Good::query()->findOrFail($goodId);

        $additionalIds = $good->additionals;
        if (count($additionalIds) < 0) {
            return response()
                ->json([
                    'success' => true,
                    'additionals' => [],
                ]);
        }
        $additionalItemsIds = Item::query()->whereIn('good_id', $additionalIds)->pluck('id')->toArray();

        $startDateTime = Carbon::parse($startDateTimeString);

        $startDate = $startDateTime->toDateString();

        $endDateTime = Carbon::parse($endDateTimeString);

        $endDate = $endDateTime->toDateString();

        $placeholders = implode(',', array_fill(0, count($additionalItemsIds), '?'));

        $sql = "
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.id IN ($placeholders)
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < ? OR (order_items.rent_start_date = ? AND order_items.rent_start_time <= ?))
        AND
        (order_items.rent_end_date > ? OR (order_items.rent_end_date = ? AND order_items.rent_end_time >= ?))
    )
";

        $params = array_merge(
            $additionalItemsIds,
            [
                $endDateString,
                $endDateString,
                $endTimeString,
                $startDateString,
                $startDateString,
                $startTimeString
            ]
        );
        $unavailableOrderItemsIds = DB::select($sql, $params);

        $unavailableOrderItemsIds = array_map(function ($item) {
            return $item->item_id;
            }, $unavailableOrderItemsIds);

        $unavailableAdditionalIds = Item::query()
            ->whereIn('id', $unavailableOrderItemsIds)
            ->pluck('good_id')->toArray();

        $availableGoods = Item::query()
            ->select('good_id', DB::raw('MAX(id) as id'))
            ->whereIn('good_id', $good->additionals)
            ->groupBy('good_id')
            ->with('good')
            ->get();

        $availableGoods->each(function ($availableGood) use ($unavailableAdditionalIds) {
            $availableGood->available = ! in_array($availableGood->good_id, $unavailableAdditionalIds);
        });

        $availableGoods = $availableGoods->toArray();

        $responseData = [
            'success' => true,
            'additionals' => $availableGoods,
        ];

        $response = response()->json($responseData);

        $cartData = json_decode($request->cookie('cart', '{}'), true);

        $cartData[$request->input('cartKey')] = [];

        $response->withCookie(cookie('cart', json_encode($cartData)));

        return $response;
    }

    public function additionalAdd(Request $request)
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        $cartKey = $request->input('cart_key');
        $additionalId = $request->input('additional_id', []);

        if (array_key_exists($cartKey, $cartData)) {
            $cartData[$cartKey][] = $additionalId;
        }

        return response()
            ->json(['success' => true])
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function additionalRemove(Request $request)
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        $cartKey = $request->input('cart_key');
        $additionalId = $request->input('additional_id', []);

        if (array_key_exists($cartKey, $cartData)) {
            unset($cartData[$cartKey][array_search($additionalId, $cartData[$cartKey])]);
        }

        return response()
            ->json(['success' => true])
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function countCostWithAdditionals($item, $cartData)
    {
        $cost = $item->good->discount_cost ?? $item->good->cost;
        if ($item->good->additionals != '[]') {
            foreach ($item->good->getAdditionals() as $additional) {
                if (in_array($additional->id, $cartData[$item->good->id.'pixelrental'.$item->id])) {
                    $cost += $additional->additional_cost ?? $additional->cost;
                }
            }
        }

        return $cost;
    }
}
