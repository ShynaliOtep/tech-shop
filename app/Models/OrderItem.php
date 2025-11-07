<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Screen\AsSource;

final class OrderItem extends Model
{
    use AsSource, Filterable, HasFactory;

    protected $guarded = [];

    protected $allowedFilters = [
        'item_id' => Where::class,
        'order_id' => Where::class,
        'amount_of_days' => WhereMaxMin::class,
        'status' => Where::class,
        'amount_paid' => WhereMaxMin::class,
        'rent_start_date' => WhereDateStartEnd::class,
        'rent_end_date' => WhereDateStartEnd::class,
        'set_good_id' => Where::class,
    ];

    protected $allowedSorts = [
        'item_id',
        'order_id',
        'status',
        'amount_paid',
        'rent_start_date',
        'rent_end_date',
        'set_good_id'
    ];

    protected $casts = [
        'additionals' => 'json',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function getAdditionals(): Collection
    {
        return Item::whereIn('id', $this->additionals)->get();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function convertToArray()
    {
        return [
            'item_id' => $this->item_id,
            'order_id' => $this->order_id,
            'amount_of_days' => $this->amount_of_days,
            'status' => $this->status,
            'amount_paid' => $this->amount_paid,
            'rent_start_date' => Carbon::parse($this->rent_start_date)->format('Y-m-d H:i:s'),
            'rent_end_date' => Carbon::parse($this->rent_end_date)->format('Y-m-d H:i:s'),
            'set_good_id' => $this->set_good_id,
        ];
    }
}
