<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Attachment;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Screen\AsSource;

/**
 * Table: clients
 *
 * === Columns ===
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property int $discount
 * @property string $email
 * @property string $iin
 * @property string $instagram
 * @property string $confirmation_code
 * @property bool $email_confirmed
 * @property bool $blocked
 * @property string $password
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * === Relationships ===
 * @property-read Favorite|null $favorites
 * @property-read Order[]|\Illuminate\Database\Eloquent\Collection $orders
 * @property-read Attachment[]|\Illuminate\Database\Eloquent\Collection $attachment
 */
class Client extends Authenticatable
{
    use AsSource, Attachable, Authorizable, Filterable, HasFactory;

    public const RESET_PASSWORD_CACHE_KEY = 'client-reset-password';
    protected $guarded = [];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'phone' => Like::class,
        'discount' => WhereMaxMin::class,
        'email' => Like::class,
        'instagram' => Like::class,
        'email_confirmed' => Where::class,
        'blocked' => Where::class,
        'created_at' => WhereDateStartEnd::class,
        'deleted_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'phone',
        'discount',
        'email',
        'instagram',
        'email_confirmed',
        'blocked',
        'created_at',
        'deleted_at',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function signature()
    {
        $this->load('attachment');

        return $this->attachment()->where('group', '=', 'signatures')->first();
    }

    public function idCards(): Collection
    {
        $this->load('attachment');

        return $this->attachment()->where('group', '=', 'idCards')->get();
    }

    public function getReferralLink(): string
    {
        $secret = env('REFERRAL_SECRET', 'mysecretkey'); // Используем секретный ключ
        $refCode = base64_encode($this->id . '|' . $secret);
        return url('/auth/register?ref=' . urlencode($refCode));
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BonusTransaction::class,   'user_id', 'id');
    }

    public function withdrawRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BonusWithdrawRequest::class,   'user_id', 'id');
    }

    public function getAvailableBalance()
    {
        $totalDeposits = $this->transactions()->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $this->transactions()->where('type', 'withdraw')->sum('amount');
        $pendingWithdrawals = $this->withdrawRequests()->where('status', 'pending')->sum('amount');

        return $totalDeposits - ($totalWithdrawals + $pendingWithdrawals);
    }

    public function bonus(): HasOne
    {
        return $this->hasOne(Bonus::class, 'user_id', 'id');
    }


    public function getBonus()
    {
        $bonus = $this->bonus;
        if ($bonus) {
            return $bonus;
        }

        $bonus = new Bonus();
        $bonus->user_id = $this->id;
        $bonus->balance = 0;
        $bonus->level = 1;
        $bonus->save();
        return $bonus;

    }

    public static function getBonusLevel($clientId)
    {
        $client = Client::find($clientId);
        $model = $client->bonus;
        if ($model) {
            return $model->level;
        }

        $model = new Bonus();
        $model->user_id = $client->id;
        $model->balance = 0;
        $model->level = 1;
        $model->save();
        return $model->level;
    }

    public static function getBonusLevelPercent($clientId)
    {
        $level = self::getBonusLevel($clientId);
        $bonusPercentage = match ($level) {
            1 => 5, // 5% на 1 уровне
            2 => 7, // 7% на 2 уровне
            3 => 10, // 10% на 3 уровне
        };

        return $bonusPercentage;

    }
}
