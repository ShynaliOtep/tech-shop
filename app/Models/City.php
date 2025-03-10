<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class City extends Model
{
    use HasFactory;

    public const DEFAULT = 1;

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public static function getPlatformCity()
    {
        return session('selected_city', 'Алматы');
    }

    public static function setSiteCity($city)
    {
        session()->put('select_city', $city);
    }

    public static function getSiteCity()
    {
        return session()->get('select_city');
    }
}
