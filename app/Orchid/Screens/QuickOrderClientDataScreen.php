<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Client;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class QuickOrderClientDataScreen extends Screen
{
    public $name = 'Быстрое оформление заказа';

    public function layout(): iterable
    {
        if ($this->query()['client_type'] == 'client') {
            $forms[] =  Relation::make('client_id')
                ->fromModel(Client::class, 'name')
                ->help(__('translations.Order client help'))
                ->required()
                ->title(__('translations.Client'))
                ->ajax('asyncUpdateFields');
        } else {
            $forms[] = Input::make('phone')
                ->title('Номер телефона')
                ->mask('+7 (999) 999-99-99') // Формат маски номера
                ->required();
            $forms[] = Input::make('instagram')
                ->title('Инстаграм')
                ->required();
        }
        $forms[] = Input::make('client_type')
            ->value($this->query()['client_type'])
            ->hidden();
        return [
            Layout::rows(array_merge( $forms ,[
                Select::make('status')
                    ->options([
                        'returned' => __('translations.returned'),
                        'in_rent' => __('translations.in_rent'),
                        'waiting' => __('translations.waiting'),
                        'confirmed' => __('translations.confirmed'),
                        'cancelled' => __('translations.cancelled'),
                    ])
                    ->title(__('translations.Status'))
                    ->help(__('translations.Order status help')),

                Select::make('paid_status')
                    ->options([
                        'pending' => __('Ожидается'),
                        'paid' => __('Оплачен'),
                        'unpaid' => __('Не оплачен'),
                    ])
                    ->title(__('Статус оплаты')),
                //  ->help(__('translations.Order status help')),

                Input::make('amount_unpaid')
                    ->title(__('Не оплаченная сумма'))
                    //  ->help(__('translations.Order agreement id help')),
                    ->type('number'),
                Button::make('Дальше')
                    ->method('next')
            ])),
        ];
    }

    public function next(Request $request)
    {
        return redirect()->route('platform.quick-order', [
            'client_type' => $request->get('client_type'),
            'client_id' => $request->get('client_id'),
            'phone' => $request->get('phone'),
            'instagram' => $request->get('instagram'),
            'status' => $request->get('status'),
            'paid_status' => $request->get('paid_status'),
            'amount_unpaid' => $request->get('amount_unpaid'),
        ]);
    }

    public function query(): array
    {
        return [
            'client_type' => request()->get('client_type'),
        ];
    }
}
