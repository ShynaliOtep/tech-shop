<?php

namespace App\Http\Controllers;

use App\Models\BonusTransaction;
use App\Models\BonusWithdrawRequest;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function requestWithdraw(Request $request)
    {
        $user = auth()->user();

        if ($user->bonus->balance < 100) {
            return response()->json(['message' => 'Минимальная сумма вывода — 100 бонусов.'], 400);
        }

        $orders = BonusTransaction::where('user_id', $user->id)->get(['order_id', 'amount']);

        BonusWithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => $user->bonus->balance,
            'orders' => $orders->toJson(),
        ]);

        // Обнуляем баланс
        $user->bonus->update(['balance' => 0]);

        return response()->json(['message' => 'Запрос на вывод отправлен.']);
    }

}
