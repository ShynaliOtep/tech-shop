<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Filters\Types\WhereIn;
use Orchid\Screen\AsSource;

/**
 * Table: items
 *
 * === Columns ===
 *
 * @property int $id
 * @property int $good_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read Good|null $good
 *
 * === Accessors/Attributes ===
 * @property-read string $name
 */
class Item extends Model
{
    use AsSource, Filterable, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $allowedFilters = [
        'id' => WhereIn::class,
        'good_id' => Where::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'good_id',
        'created_at',
        'deleted_at',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }

    public function getNameAttribute(): string
    {
        return $this->good['name_'.session()->get('locale', 'ru')]." ($this->id)";
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
