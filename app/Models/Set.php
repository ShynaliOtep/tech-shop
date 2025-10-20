<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Screen\AsSource;

class Set extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    protected $guarded = [];

    protected $allowedFilters = [
        'good_id' => Where::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'good_id',
        'created_at',
        'deleted_at',
    ];

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }


    public function goods()
    {
        return $this->belongsToMany(Good::class, 'sets_goods', 'set_id', 'good_id');
    }
}
