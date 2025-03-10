<?php

namespace App\Services\Bonus;

use App\Models\BonusTransaction;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;

class BonusSystem
{
    public function applyBonus(Client $user, Order $order)
    {
        $bonusPercentage = match ($user->bonus->level) {
            1 => 5, // 5% на 1 уровне
            2 => 7, // 7% на 2 уровне
            3 => 10, // 10% на 3 уровне
        };

        $bonusAmount = $order->amount_paid * ($bonusPercentage / 100); // Получаем сумму заказа

        // Записываем бонусную транзакцию
        BonusTransaction::create([
            'user_id'  => $user->id,
            'order_id' => $order->id, // Теперь ID заказа доступен
            'type' => 'deposit',
            'amount'   => $bonusAmount,
        ]);

        // Обновляем баланс и общую сумму заработанных бонусов
        $user->bonus->increment('balance', $bonusAmount);
        $user->bonus->increment('total_earned', $bonusAmount);

        // Начисляем бонус рефереру, если есть
        if ($user->referrer_id) {
            $this->applyReferralBonus($user->referrer_id, $order);
        }

        // Проверяем уровень
        $this->updateUserBonusLevel($user);
    }

    private function updateUserBonusLevel(Client $user)
    {
        $totalEarned = $user->bonus->total_earned;

        if ($totalEarned >= 1000) {
            $user->bonus->update(['level' => 3]);
        } elseif ($totalEarned >= 500) {
            $user->bonus->update(['level' => 2]);
        }
    }

    private function applyReferralBonus(int $referrerId, Order $order)
    {
        $referrer = Client::find($referrerId);

        if (!$referrer) {
            return;
        }

        $referralBonus = $order->amount_paid * 0.03;

        BonusTransaction::create([
            'user_id'  => $referrer->id,
            'order_id' => $order->id,
            'type' => 'deposit',
            'amount'   => $referralBonus,
        ]);

        $referrer->bonus->increment('balance', $referralBonus);
    }
}
