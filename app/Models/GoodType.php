<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

/**
 * Table: good_types
 *
 * === Columns ===
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $icon
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read Good[]|\Illuminate\Database\Eloquent\Collection $goods
 */
class GoodType extends Model
{
    use AsSource, Filterable, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $allowedFilters = [
        'name' => Like::class,
        'description' => Like::class,
        'code' => Like::class,
        'icon' => Like::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'name',
        'description',
        'code',
        'icon',
        'created_at',
        'deleted_at',
    ];

    public function goods(): HasMany
    {
        return $this->hasMany(Good::class)->orderByDesc('priority');
    }
}
