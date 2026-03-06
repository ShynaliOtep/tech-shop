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
