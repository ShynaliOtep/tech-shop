<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $discount_cost
 * @property int $good_id
 * @property int $city_id
 */
class GoodPrice extends Model
{
    use HasFactory;

    protected $guarded = [];
}
