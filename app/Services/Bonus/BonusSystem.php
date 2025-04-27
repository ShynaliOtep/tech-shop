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
        $bonusLevel = BonusLevels::levelMatch($user->bonus->level);

        $percent = $bonusLevel->percent;

        if ($user->bonus_percent  && $user->bonus_percent > 0) {
            $percent = $user->bonus_percent;
        }

        $bonusAmount = $order->amount_paid * ($percent / 100); // Получаем сумму заказа

        // Записываем бонусную транзакцию
        BonusTransaction::create([
            'user_id'  => $user->id,
            'order_id' => $order->id, // Теперь ID заказа доступен
            'type' => 'deposit',
            'amount'   => $bonusAmount,
            'source' => 'order'
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
        $bonusLevel = BonusLevels::getLevelFromEarned($totalEarned);
        $user->bonus->update(['level' => $bonusLevel->level]);
    }

    private function applyReferralBonus(int $referrerId, Order $order)
    {
        $referrer = Client::find($referrerId);

        if (!$referrer) {
            return;
        }

        $referralBonus = $order->amount_paid * 0.10;

        BonusTransaction::create([
            'user_id'  => $referrer->id,
            'order_id' => $order->id,
            'type' => 'deposit',
            'amount'   => $referralBonus,
            'source' => 'referral'
        ]);

        $referrer->bonus->increment('balance', $referralBonus);
    }
}
