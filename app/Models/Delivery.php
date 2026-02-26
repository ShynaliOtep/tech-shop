<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $order_id
 * @property string $street
 * @property string $house
 * @property ?string $entrance
 * @property ?string $floor
 * @property ?string $comment
 * @property ?string $apartment
 */
class Delivery extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;
    protected $guarded = [];
}
