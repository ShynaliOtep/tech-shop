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

class SetsGood extends Model
{
    use AsSource, Filterable, HasFactory;


    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $allowedFilters = [
        'id' => WhereIn::class,
        'set_id' => Where::class,
        'good_id' => Where::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'good_id',
        'set_id',
        'created_at',
        'deleted_at',
    ];

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }
}
