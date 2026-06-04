<?php

namespace App\Orchid\Screens;

use App\Models\Client;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\DateTimer;

class QuickOrderClientDataScreen extends Screen
{
    public $name = "Быстрое оформление заказа";

    public function layout(): iterable
    {
        $forms = [];
        if ($this->query()["client_type"] == "client") {
            $forms[] = Relation::make("client_id")
                ->fromModel(Client::class, "name")
                ->help(__("translations.Order client help"))
                ->required()
                ->title(__("translations.Client"));
        } else {
            $forms[] = Input::make("phone")
                ->title("Номер телефона")
                ->mask("+7 (999) 999-99-99") // Формат маски номера
                ->required();
            $forms[] = Input::make("instagram")->title("Инстаграм")->required();
        }
        $forms[] = Input::make("client_type")
            ->value($this->query()["client_type"])
            ->hidden();

        if ($this->query()["time_type"] == "all") {
            $forms[] = DateTimer::make("rent_start_date")
                ->title(__("translations.Rent start date"))
                ->placeholder(__("translations.OrderItem rent_start help"))
                ->required()
                ->help(__("translations.OrderItem rent_start help"))
                ->format("Y-m-d");

            $forms[] = Select::make("rent_start_time")
                ->options($this->generateTimeSpans())
                ->required()
                ->title(__("translations.Rent start time"))
                ->help(__("translations.OrderItem rent_start_time help"));

            $forms[] = DateTimer::make("rent_end_date")
                ->title(__("translations.Rent end date"))
                ->required()
                ->placeholder(__("translations.OrderItem rent_end help"))
                ->help(__("translations.OrderItem rent_end help"))
                ->format("Y-m-d");

            $forms[] = Select::make("rent_end_time")
                ->options($this->generateTimeSpans())
                ->required()
                ->title(__("translations.Rent end time"))
                ->help(__("translations.OrderItem rent_end_time help"));
        }
        return [
            Layout::rows(
                array_merge($forms, [
                    Select::make("status")
                        ->options([
                            "returned" => __("translations.returned"),
                            "in_rent" => __("translations.in_rent"),
                            "waiting" => __("translations.waiting"),
                            "confirmed" => __("translations.confirmed"),
                            "cancelled" => __("translations.cancelled"),
                        ])
                        ->title(__("translations.Status"))
                        ->help(__("translations.Order status help")),

                    Select::make("paid_status")
                        ->options([
                            "pending" => __("Ожидается"),
                            "paid" => __("Оплачен"),
                            "unpaid" => __("Не оплачен"),
                        ])
                        ->title(__("Статус оплаты")),

                    Input::make("amount_unpaid")
                        ->title(__("Не оплаченная сумма"))
                        ->type("number"),
                    Button::make("Дальше")->method("next"),
                ]),
            ),
        ];
    }

    public function next(Request $request)
    {
        return redirect()->route("platform.quick-order", [
            "client_type" => $request->get("client_type"),
            "client_id" => $request->get("client_id"),
            "phone" => $request->get("phone"),
            "instagram" => $request->get("instagram"),
            "status" => $request->get("status"),
            "paid_status" => $request->get("paid_status"),
            "amount_unpaid" => $request->get("amount_unpaid"),
            "rent_end_time" => $request->get("rent_end_time")
                ? $request->get("rent_end_time")
                : null,
            "rent_start_time" => $request->get("rent_start_time")
                ? $request->get("rent_start_time")
                : null,
            "rent_end_date" => $request->get("rent_end_date")
                ? $request->get("rent_end_date")
                : null,
            "rent_start_date" => $request->get("rent_start_date")
                ? $request->get("rent_start_date")
                : null,
        ]);
    }

    public function query(): array
    {
        return [
            "client_type" => request()->get("client_type"),
            "time_type" => request()->get("time_type"),
        ];
    }

    public function generateTimeSpans()
    {
        $arr = [];
        for ($hours = 0; $hours < 24; $hours++) {
            for ($minutes = 0; $minutes < 60; $minutes += 5) {
                $hoursStr = str_pad($hours, 2, "0", STR_PAD_LEFT);
                $minutesStr = str_pad($minutes, 2, "0", STR_PAD_LEFT);

                $arr["$hoursStr:$minutesStr:00"] = "$hoursStr:$minutesStr:00";
            }
        }

        return $arr;
    }
}
