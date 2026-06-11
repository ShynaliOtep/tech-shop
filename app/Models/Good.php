<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

/**
 * Table: goods
 *
 * === Columns ===
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_en
 * @property string $description_ru
 * @property string $description_en
 * @property int $cost
 * @property int|null $additional_cost
 * @property int|null $discount_cost
 * @property int $damage_cost
 * @property string|null $related_goods

 * @property int $good_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read GoodType|null $goodType
 * @property-read Item[]|\Illuminate\Database\Eloquent\Collection $items
 * @property-read Good[]|\Illuminate\Database\Eloquent\Collection $relatedGoods
 * @property-read Attachment[]|\Illuminate\Database\Eloquent\Collection $attachment
 */
class Good extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    protected $guarded = [];

    protected $allowedFilters = [
        'is_set' => Where::class,
        'name_ru' => Like::class,
        'name_en' => Like::class,
        'cost' => WhereMaxMin::class,
        'discount_cost' => WhereMaxMin::class,
        'additional_cost' => WhereMaxMin::class,
        'damage_cost' => WhereMaxMin::class,
        'good_type_id' => Where::class,
        'description_ru' => Like::class,
        'description_en' => Like::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'is_set',
        'name_ru',
        'name_en',
        'cost',
        'discount_cost',
        'additional_cost',
        'priority',
        'damage_cost',
        'good_type_id',
        'description_ru',
        'description_en',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'related_goods' => 'json',
        'additionals' => 'json',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_REPAIR = 'repair';

    public function goodType(): BelongsTo
    {
        return $this->belongsTo(GoodType::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function itemsByCity(): HasMany
    {
        $cityId = City::getSiteCity();
        if (!$cityId) {
            $cityId = City::DEFAULT;
        }
        return $this->hasMany(Item::class)->where('city_id', $cityId);
    }

    public function relatedGoods(): HasMany
    {
        return $this->hasMany(Good::class, 'id', 'related_goods');
    }

    public function additionals(): HasMany
    {
        return $this->hasMany(Good::class, 'id', 'additionals');
    }

    public function getAdditionals(): Collection
    {
        return Good::whereIn('id', $this->additionals)->get();
    }

    public function getRelatedGoods(): Collection
    {
        return Good::whereIn('id', $this->related_goods)->get();
    }


    public function availableCount(): int
    {
        if (!$this->is_set) {
            return $this->itemsByCity()->count();
        }

        $set = Set::where('good_id', $this->id)->first();
        if (!$set) {
            return $this->itemsByCity()->count();
        }

        $min = 10000;

        foreach ($set->goods as $good) {
            $count = $good->itemsByCity()->count();
            $min =  min($count, $min);
        }

        return $min;
    }

    public function platformPrice(): ?GoodPrice
    {
        return GoodPrice::where(['good_id'=> $this->id, 'city_id' => City::getPlatformCity()])->first();
    }

    public function sitePrice(): ?GoodPrice
    {
        return GoodPrice::where(['good_id'=> $this->id, 'city_id' => City::getSiteCity()])->first();
    }


    public function getDiscountCost(string $platform = 'site'): ?int
    {
        if ($platform == 'site') {
            $price = $this->sitePrice();
        } else {
            $price = $this->platformPrice();
        }

        return $price ? $price->discount_cost : $this->discount_cost;
    }

    /**
     * Итоговая цена основного товара для конкретного клиента.
     *
     * Правило: скидки не суммируются — берётся наиболее выгодная цена:
     *   - собственная скидка товара (getDiscountCost)
     *   - персональная скидка клиента (getDiscountPercent) от базовой цены
     */
    public function getPriceForClient(Client $client, string $platform = 'site'): int
    {
        $basePrice        = $this->cost;
        $goodDiscountPrice = $this->getDiscountCost($platform);
        $clientPercent    = $client->getDiscountPercent();

        $clientPrice = $clientPercent > 0
            ? (int) round($basePrice * (1 - $clientPercent / 100))
            : null;

        $candidates = array_filter(
            [$goodDiscountPrice, $clientPrice, $basePrice],
            fn($p) => $p !== null,
        );

        return min($candidates);
    }

    /**
     * Итоговая цена дополнительного товара для конкретного клиента.
     *
     * Использует additional_cost (если задан) или cost как базу,
     * применяет ту же логику приоритетов, что и getPriceForClient.
     */
    public function getAdditionalPriceForClient(Client $client): int
    {
        $basePrice     = $this->additional_cost > 0 ? $this->additional_cost : $this->cost;
        $clientPercent = $client->getDiscountPercent();

        $clientPrice = $clientPercent > 0
            ? (int) round($basePrice * (1 - $clientPercent / 100))
            : null;

        $candidates = array_filter(
            [$clientPrice, $basePrice],
            fn($p) => $p !== null,
        );

        return min($candidates);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSimple(Builder $query)
    {
        return $query->where('is_set', 0);
    }
}
