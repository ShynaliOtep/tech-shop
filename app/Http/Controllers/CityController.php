<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\City;

class CityController extends Controller
{
    public function selectCity(Request $request)
    {
        $cityId = $request->input('city_id');
        $city = City::findOrFail($cityId);

        // Сохраняем город в сессию и куки
        Session::put('selected_city', $city->id);
        Cookie::queue('selected_city', $city->id, 60 * 24 * 30); // 30 дней

        return redirect()->back()->with('success', "Город установлен: {$city->name}");
    }
}

