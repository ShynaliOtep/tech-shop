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
        session()->put('is_select_city', true);
        session()->save();
        cookie()->queue(cookie('select_city', (int)$city, 60 * 24 * 30));

        // Без Referer back() берёт последний URL из сессии — это бывает /api/... (JSON)
        $previous = url()->previous();
        if (str_contains(parse_url($previous, PHP_URL_PATH) ?? '', '/api/')) {
            return redirect('/');
        }

        return redirect()->to($previous);
    }
}

