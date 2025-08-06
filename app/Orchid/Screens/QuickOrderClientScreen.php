<?php

namespace App\Orchid\Screens;

use App\Models\City;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class QuickOrderClientScreen extends Screen
{
    public $name = 'Быстрое оформление заказа';
    public function query(): array
    {
        return [
        ];
    }
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Select::make('client_type')
                    ->title('Для кого')
                    ->options([
                        'client' => 'Для клиента',
                        'guest' => 'Для гостя'
                    ]),
                Button::make('Дальше')
                    ->method('next')
            ]),
        ];
    }

    public function next(Request $request)
    {
        return redirect()->route('platform.quick-order-client-data', ['client_type' => $request->get('client_type')]);
    }
}
