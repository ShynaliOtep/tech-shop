<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class QuickOrderClientScreen extends Screen
{
    public $name = "Быстрое оформление заказа";
    public function query(): array
    {
        return [];
    }
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Select::make("client_type")
                    ->title("Для кого")
                    ->options([
                        "client" => "Для клиента",
                        "guest" => "Для гостя",
                    ]),
                Select::make("time_type")
                    ->title("Указать время")
                    ->options([
                        "all" => __("Для всех товаров"),
                        "individuale" => __("Индивидуальное"),
                    ]),

                Button::make("Дальше")->method("next"),
            ]),
        ];
    }

    public function next(Request $request)
    {
        return redirect()->route("platform.quick-order-client-data", [
            "client_type" => $request->get("client_type"),
            "time_type" => $request->get("time_type"),
        ]);
    }
}
