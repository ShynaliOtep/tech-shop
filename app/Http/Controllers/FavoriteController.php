<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Favorite;
use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function getFavorites(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $clientId = $client->id;

        $favoriteGoodIds = Favorite::query()->where('client_id', '=', $clientId)->pluck('good_id')->toArray();

        $goods = Good::query()->whereIn('id', $favoriteGoodIds)->get();

        return view('good', compact('goods'));
    }

    public function add(Good $good)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $clientId = $client->id;

        Favorite::query()
            ->create([
                'good_id' => $good->id,
                'client_id' => $clientId,
            ]);

        return response()
            ->json(['success' => true]);
    }

    public function remove(Good $good)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $clientId = $client->id;
        Favorite::query()->where('client_id', '=', $clientId)
            ->where('good_id', '=', $good->id)->delete();

        return response()
            ->json(['success' => true]);
    }
}
