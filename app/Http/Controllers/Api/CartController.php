<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $cartData = json_decode($request->cookie('cart', '{}'), true);

        $itemIds = [];
        $counts = [];

        foreach ($cartData as $key => $value) {
            $itemIds[] = $key;
            $counts[$key] = $value['quantity'];
        }

        $items = Item::query()->whereIn('id', $itemIds)->get();

        $client = Auth::guard('clients')->check()
            ? Client::query()->find(Auth::guard('clients')->id())->toArray()
            : null;

        return response()->json(compact('items', 'cartData', 'client', 'counts'))
            ->cookie('cart', json_encode($cartData), 60 * 24 * 30);
    }

    public function cartItems(Request $request)
    {
        $itemIds = $request->get('items');

        $items = Item::query()->whereIn('id', $itemIds)->with('good.attachment')->get();

        return response()->json([
            'items' => $items,
        ]);
    }

}
