<?php

namespace App\Orchid\Screens;

use App\Models\City;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

class CitySelectorScreen extends Screen
{
    public $name = 'Выбор города';

    public function query(): array
    {
        return [
            'cities' => City::pluck('name', 'id')->toArray(), // Загружаем список городов
            'selected_city' => session('selected_city', null),
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Select::make('selected_city')
                    ->title('Выберите город')
                    ->options($this->query()['cities']) // Используем города из запроса
                    ->value(session('selected_city')),
                Button::make('Сохранить')
                    ->method('save')

            ]),
        ];
    }

    public function save(Request $request)
    {
        session(['selected_city' => City::find($request->get('selected_city'))->id]);

        return redirect()->route('platform.index'); // Перенаправление на главную
    }
}
