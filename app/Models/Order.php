<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Screen\AsSource;

/**
 * Table: orders
 *
 * === Columns ===
 *
 * @property int $id
 * @property int $client_id
 * @property int|null $amount_paid
 * @property int|null $agreement_id
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read Client|null $owner
 * @property-read OrderItem|null $orderItems
 * @property-read Item[]|\Illuminate\Database\Eloquent\Collection $items
 * @property-read Attachment[]|\Illuminate\Database\Eloquent\Collection $attachment
 */
class Order extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m:s',
        'updated_at' => 'datetime:Y-m-d h:m:s',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'client_id' => Where::class,
        'agreement_id' => Where::class,
        'amount_paid' => WhereMaxMin::class,
        'status' => Where::class,
        'created_at' => WhereDateStartEnd::class,
        'updated_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
        'rent_start_date' => WhereDateStartEnd::class,
        'rent_end_date' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'client_id',
        'agreement_id',
        'amount_paid',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'rent_start_date',
        'rent_end_date',
    ];

    public function owner(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'order_items');
    }

    public function rentStartDate()
    {
        $date = Carbon::parse($this->orderItems()->orderBy('rent_start_date', 'ASC')->first()->rent_start_date);

        return '«'.$date->day.'» '.str_pad($date->month, 2, '0', STR_PAD_LEFT).' '.$date->year;
    }

    public function rentEndDate()
    {
        $date = Carbon::parse($this->orderItems()->orderBy('rent_start_date', 'DESC')->first()->rent_end_date);

        return '«'.$date->day.'» '.str_pad($date->month, 2, '0', STR_PAD_LEFT).' '.$date->year;
    }

    public function totalDamageCost()
    {
        $totalSum = 0;

        foreach ($this->orderItems as $orderItem){
            $totalSum += $orderItem->item->good->damage_cost;
        }

        return $totalSum;
    }
}
