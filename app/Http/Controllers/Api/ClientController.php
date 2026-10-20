<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $isAuthenticated = (bool) Auth::guard('clients')->id();
        if ($isAuthenticated) {
            /**
             * @var Client $client
             */
            $client = Client::query()->find(Auth::guard('clients')->id());
            $clientData = $client->toArray();
            $clientData['bonusPercent'] =  Client::getBonusLevelPercent(Auth::guard('clients')->id());
            $clientData['bonus'] = $client->getBonus()->toArray();
            $clientData['order_count'] = $client->getOrderCount();
            // Процент, по которому BonusSystem::applyBonus начислит бонусы после возврата заказа.
            $clientData['bonusAccrualPercent'] = $client->bonus_percent > 0
                ? (float) $client->bonus_percent
                : ($client->bonus ? (float) \App\Services\Bonus\BonusLevels::levelMatch($client->bonus->level)->percent : 0);
            // Пароль, коды подтверждения и токены на фронт не отдаём.
            unset($clientData['password'], $clientData['confirmation_code'], $clientData['remember_token']);
            return response()->json([
                'isAuthenticated' => (bool) Auth::guard('clients')->id(),
                'clientData' => $clientData,
            ]);
        } else {
            return response()->json([
                'isAuthenticated' => false,
                'clientData' => null,
            ]);
        }
    }
}
