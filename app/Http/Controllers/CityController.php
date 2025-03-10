<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\City;

class CityController extends Controller
{
    public function selectCity(Request $request, string $city)
    {
        // Сохраняем город в сессию и куки
        session()->put('select_city', (int) $city);
        session()->save();
        cookie()->queue(cookie('select_city', (int)$city, 60 * 24 * 30));

        return redirect()->back();
    }
}

