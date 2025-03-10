<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class BonusWithdrawRequest extends Model
{
    use HasFactory,Filterable, Attachable, AsSource;

    protected $guarded = [];

    public function getTransactionsForWithdraw()
    {
        $transactions = $this->hasMany(BonusTransaction::class, 'user_id', 'user_id')
            ->where('type', 'deposit')
            ->where('created_at', '<=', $this->created_at)
            ->orderByDesc('created_at')
            ->get();

        $filteredTransactions = [];
        $totalAmount = 0;

        foreach ($transactions as $transaction) {
            $totalAmount += $transaction->amount;
            $filteredTransactions[] = $transaction;

            // Если сумма достигла или превысила сумму вывода, останавливаемся
            if ($totalAmount >= $this->amount) {
                break;
            }
        }

        return collect($filteredTransactions);
    }

}
