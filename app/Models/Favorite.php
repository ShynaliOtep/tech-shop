<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table: favorites
 *
 * === Columns ===
 *
 * @property int $id
 * @property int $good_id
 * @property int $client_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read Good|null $good
 * @property-read Client|null $client
 */
class Favorite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'good_id', 'client_id',
    ];

    /**
     * Get the good that owns the favorite.
     */
    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    /**
     * Get the client that owns the favorite.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
