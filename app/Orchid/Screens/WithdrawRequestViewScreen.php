<?php

namespace App\Orchid\Screens;

use App\Models\BonusWithdrawRequest;
use App\Models\BonusTransaction;
use App\Models\Client;
use App\Models\Order;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class WithdrawRequestViewScreen extends Screen
{
    public $name = 'Детали заявки на вывод';
    public $description = 'Список транзакций, связанных с заявкой';
    public $request;

    public function query($id): iterable
    {
        $this->request = BonusWithdrawRequest::findOrFail($id);

        return [
            'transactions' => $this->request->getTransactionsForWithdraw()
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('✅ Подтвердить')
                ->method('approve')
                ->canSee($this->request->status === 'pending'),

            Button::make('❌ Отклонить')
                ->method('reject')
                ->canSee($this->request->status === 'pending'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('transactions', [
                TD::make('id', 'ID')->sort(),
                TD::make('user_id', 'Пользователь'),
                TD::make('order_id', 'Заказ')
//                    ->filter(
//                        Relation::make()
//                            ->fromModel(Order::class, 'id')
//                    )
                    ->render(function (BonusTransaction $transaction) {
                        if ($transaction->order_id) {
                            return Link::make($transaction->order->id)
                                ->route('platform.orders.edit', $transaction->order_id);
                        } else {
                            return null;
                        }
                    }),
                TD::make('amount', 'Сумма')->render(fn ($t) => number_format($t->amount, 2) . ' ₸'),
                TD::make('type', 'Тип')
                    ->render(function (BonusTransaction $transaction) {
                        return __('translations.'.$transaction->type);
                    }),
                TD::make('source', 'Источник')
                    ->render(function (BonusTransaction $transaction) {
                        if ($transaction->source) {
                            return __('translations.'.$transaction->source);
                        }
                        return null;
                    }),
                TD::make('created_at', 'Дата')->sort(),
            ]),
        ];
    }

    public function approve()
    {
        $this->request->update(['status' => 'approved']);
        BonusTransaction::create([
            'user_id'  => $this->request->user_id,
            'type'     => 'withdraw',
            'amount'   => $this->request->amount,
        ]);
        Alert::info('Заявка подтверждена.');
    }

    public function reject()
    {
        $this->request->update(['status' => 'rejected']);
        Alert::info('Заявка отклонена.');
    }
}
