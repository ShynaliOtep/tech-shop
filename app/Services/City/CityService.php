<?php

namespace App\Services\City;

use App\Models\City;

class CityService
{
    public static function city()
    {
        $cityId = session()->get('select_city');
        return $cityId ?: City::DEFAULT;
    }
}
