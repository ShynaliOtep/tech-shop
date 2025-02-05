<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyOrderController extends Controller
{
    public function getMyOrders(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $clientId = $client->id;

        $orders = Order::query()->where('client_id', '=', $clientId)->with('orderItems')->get();

        return view('orderList', compact('orders'));
    }

    public function viewOrder(Request $request, Order $order)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $order->load('orderItems.item.good.goodType');

        return view('orderView', compact('order'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
            return redirect(route('logout'));
        }

        DB::beginTransaction();
        try {
            $order->status = 'cancelled';
            $order->save();

            $order->orderItems()->update(['status' => 'cancelled']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()
                ->json(['success' => false,
                    'message' => 'Не удалось отменить заказ']);
        }

        return response()
            ->json(['success' => true]);
    }
}
