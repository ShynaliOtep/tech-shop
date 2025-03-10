<?php

namespace App\Orchid\Screens;

use App\Models\BonusWithdrawRequest;
use App\Models\Order;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class WithdrawRequestScreen extends Screen
{
    public $name = 'Заявки на вывод';
    public $description = 'Просмотр и управление заявками на вывод';

    public function query(): iterable
    {
        return [
            'withdrawRequests' => BonusWithdrawRequest::filters()->defaultSort('id', 'DESC')->paginate(),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('withdrawRequests', [
                TD::make('id', 'ID')->sort(),
                TD::make('user_id', 'Пользователь'),
                TD::make('amount', 'Сумма')
                    ->render(fn ($request) => number_format($request->amount, 2) . ' ₸'),
                TD::make('status', 'Статус')
                    ->render(fn ($request) => ucfirst($request->status)),
                TD::make('created_at', 'Дата')->sort(),
                TD::make('actions', 'Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn ($request) => Link::make('Подробнее')
                        ->route('platform.withdraw.view', $request->id)),
            ]),
        ];
    }
}
