<?php

namespace App\Orchid\Screens;

use App\Models\City;
use App\Models\Client;
use App\Models\Good;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Bonus\GoodService;
use App\Services\Good\GoodItemService;
use App\Services\Good\TimeRange;
use App\Services\Order\BookingLock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class QuickOrderScreen extends Screen
{
    public $name = "Быстрое оформление заказа";

    public $rentStartDate;
    public $requestStartTime;
    public $requstEntDate;
    public $requestEndTime;

    public $showPhoneField = false;

    // Храним состояние модалки (нужно для обновления интерфейса)
    public $showAddItemModal = false;

    public function commandBar(): array
    {
        return [
            Button::make("Оформить заказ")->method("saveOrder"),
            ModalToggle::make("Добавить товар")
                ->icon("plus")
                ->method("addItem")
                ->modal("addItemModal"),
        ];
    }

    public function layout(): iterable
    {
        $startDate = $this->query()["rent_start_date"] ?? null;
        $endDate = $this->query()["rent_end_date"] ?? null;
        $startTime = $this->query()["rent_start_time"] ?? null;
        $endTime = $this->query()["rent_end_time"] ?? null;
        $forms = [];
        // session()->forget('quick_order_items');
        if ($this->query()["client_type"] == "client") {
            $forms[] = Label::make("order.client_id")
                ->title(__("translations.Client"))
                ->value(
                    Client::query()->find($this->query()["client_id"])->name,
                );
            $forms[] = Input::make("order.client_id")
                ->value($this->query()["client_id"])
                ->style()
                ->hidden();
        } else {
            $forms[] = Label::make("order.phone")
                ->title("Номер телефона")
                ->value($this->query()["phone"]);
            $forms[] = Input::make("order.phone")
                ->value($this->query()["phone"])
                ->hidden();
            $forms[] = Label::make("order.instagram")
                ->title("Инстаграм")
                ->value($this->query()["instagram"]);
            $forms[] = Input::make("order.instagram")
                ->value($this->query()["instagram"])
                ->hidden();
        }

        if ($startDate) {
            $forms[] = Label::make("order.rent_start_date")
                ->title("Дата начала аренды")
                ->value($this->query()["rent_start_date"]);
            $forms[] = Input::make("order.rent_start_date")
                ->value($this->query()["rent_start_date"])
                ->hidden();
        }
        if ($startTime) {
            $forms[] = Label::make("order.rent_start_time")
                ->title("Время начала аренды")
                ->value($this->query()["rent_start_time"]);
            $forms[] = Input::make("order.rent_start_time")
                ->value($this->query()["rent_start_time"])
                ->hidden();
        }
        if ($endDate) {
            $forms[] = Label::make("order.rent_end_date")
                ->title("Дата конца аренды")
                ->value($this->query()["rent_end_date"]);
            $forms[] = Input::make("order.rent_end_date")
                ->value($this->query()["rent_end_date"])
                ->hidden();
        }
        if ($endTime) {
            $forms[] = Label::make("order.rent_end_time")
                ->title("Время конца аренды")
                ->value($this->query()["rent_end_time"]);
            $forms[] = Input::make("order.rent_end_time")
                ->value($this->query()["rent_end_time"])
                ->hidden();
        }
        $statuses = [
            "pending" => __("Ожидается"),
            "paid" => __("Оплачен"),
            "unpaid" => __("Не оплачен"),
        ];
        $paidStatus = $statuses[$this->query()["paid_status"]] ?? "—";

        $itemForms = [];
        if (!$startDate) {
            $itemForms[] = DateTimer::make("orderItem.rent_start_date")
                ->title(__("translations.Rent start date"))
                ->placeholder(__("translations.OrderItem rent_start help"))
                ->required()
                ->help(__("translations.OrderItem rent_start help"))
                ->format("Y-m-d");
        }
        if (!$startTime) {
            $itemForms[] = Select::make("orderItem.rent_start_time")
                ->options($this->generateTimeSpans())
                ->required()
                ->title(__("translations.Rent start time"))
                ->help(__("translations.OrderItem rent_start_time help"));
        }
        if (!$endDate) {
            $itemForms[] = DateTimer::make("orderItem.rent_end_date")
                ->title(__("translations.Rent end date"))
                ->required()
                ->placeholder(__("translations.OrderItem rent_end help"))
                ->help(__("translations.OrderItem rent_end help"))
                ->format("Y-m-d");
        }
        if (!$endTime) {
            $itemForms[] = Select::make("orderItem.rent_end_time")
                ->options($this->generateTimeSpans())
                ->required()
                ->title(__("translations.Rent end time"))
                ->help(__("translations.OrderItem rent_end_time help"));
        }

        return [
            Layout::rows(
                array_merge($forms, [
                    Label::make("order.status")
                        ->title(__("translations.Status"))
                        ->value(__("translations." . $this->query()["status"])),
                    Label::make("order.paid_status")
                        ->title(__("Статус оплаты"))
                        ->value($paidStatus),
                    Label::make("order.amount_unpaid")
                        ->title(__("Неоплаченная сумма"))
                        ->value($this->query()["amount_unpaid"]),
                    Input::make("order.status")
                        ->value($this->query()["status"])
                        ->hidden(),
                    Input::make("order.paid_status")
                        ->value($this->query()["paid_status"])
                        ->hidden(),
                    Input::make("order.amount_unpaid")
                        ->value($this->query()["amount_unpaid"])
                        ->hidden(),
                    Input::make("order.client_type")
                        ->value($this->query()["client_type"])
                        ->hidden(),
                ]),
            ),

            Layout::legend("order_info", [
                Sight::make("total_cost", "Итого")->render(
                    fn() => $this->draftTotal(),
                ),
            ]),

            Layout::table("selectedItems", [
                TD::make("item_id", "ID")->render(
                    fn($item) => $item["item_id"],
                ),
                TD::make("name", "Товар")->render(fn($item) => $item["name"]),
                TD::make("rent_start_date", "Дата начала")->render(
                    fn($item) => $item["rent_start_date"],
                ),
                TD::make("rent_start_time", "Время начала")->render(
                    fn($item) => $item["rent_start_time"],
                ),
                TD::make("rent_end_date", "Дата конца")->render(
                    fn($item) => $item["rent_end_date"],
                ),
                TD::make("rent_end_time", "Время конца")->render(
                    fn($item) => $item["rent_end_time"],
                ),
                TD::make("cost", "Цена")->render(fn($item) => $item["cost"]),
                TD::make("add_names", "Дополнения к товарам")->render(
                    fn($item) => $item["add_names"],
                ),
                TD::make(
                    "is_additional",
                    "Товар берут как дополнение?",
                )->render(fn($item) => $item["is_additional"] ? "Да" : "Нет"),
                TD::make("actions", "Действия")->render(
                    fn($item) => Button::make("Удалить")->method("removeItem", [
                        "id" => $item["id"],
                    ]),
                ),
            ])->title("Выбранные товары"),

            // ✅ Делаем модалку асинхронной, чтобы не перезагружалась страница
            Layout::modal("addItemModal", [
                Layout::rows(
                    array_merge($itemForms, [
                        Select::make("orderItem.item_id")
                            ->options($this->getOptions())
                            ->help(__("translations.OrderItem item help"))
                            ->required()
                            ->title(__("translations.Item")),
                        Select::make("orderItem.additionals")
                            ->options($this->getOptions())
                            ->multiple()
                            ->help(__("translations.OrderItem additional help"))
                            ->title(__("translations.Additionals")),
                        Select::make("orderItem.is_additional")
                            ->options([
                                false => "Нет",
                                true => "Да",
                            ])
                            ->title(__("translations.Is additional"))
                            ->required()
                            ->help(
                                __("translations.OrderItem is_additional help"),
                            ),
                    ]),
                ),
                //  ItemSelectListener::class
            ])
                ->title("Добавить товар")
                ->applyButton("Добавить")
                ->closeButton("Отмена")
                ->method("addItem"),
            // ->async('asyncUpdateAvailableItems'),
        ];
    }

    public function query(): array
    {
        $rentStartDate = request()->get("rent_start_date");
        $rentStartTime = request()->get("rent_start_time");
        $rentEndDate = request()->get("rent_end_date");
        $rentEndTime = request()->get("rent_end_time");

        if ($rentStartDate) {
            session()->put("quick_order_global_dates", [
                "rent_start_date" => $rentStartDate,
                "rent_start_time" => $rentStartTime,
                "rent_end_date" => $rentEndDate,
                "rent_end_time" => $rentEndTime,
            ]);
        }

        return [
            "client_type" => request()->get("client_type"),
            "client_id" => request()->get("client_id"),
            "phone" => request()->get("phone"),
            "instagram" => request()->get("instagram"),
            "status" => request()->get("status"),
            "paid_status" => request()->get("paid_status"),
            "amount_unpaid" => request()->get("amount_unpaid"),
            "rent_start_date" => $rentStartDate,
            "rent_start_time" => $rentStartTime,
            "rent_end_date" => $rentEndDate,
            "rent_end_time" => $rentEndTime,
            "selectedItems" => session()->get("quick_order_items", []),
        ];
    }

    public function asyncGetOptions(
        $rent_start_date,
        $rent_start_time,
        $rent_end_date,
        $rent_end_time,
    ) {
        return [
            "rent_start_date" => $rent_start_date,
            "rent_start_time" => $rent_start_time,
            "rent_end_date" => $rent_end_date,
            "rent_end_time" => $rent_end_time,
        ];
    }

    public function getOptions(): array
    {
        $cityId = City::getPlatformCity();
        $itemOptions = Good::query()
            ->whereHas("items", function ($query) use ($cityId) {
                $query->where("status", "available")->where("city_id", $cityId);
            })
            ->pluck("name_ru", "id")
            ->toArray();
        return $itemOptions;
    }

    public function asyncData(Request $request): array
    {
        return [
            "rentStartDate" => $request->input("rent_start_date"),
            "rentStartTime" => $request->input("rent_start_time"),
            "rentEndDate" => $request->input("rent_end_date"),
            "rentEndTime" => $request->input("rent_end_time"),
        ];
    }

    public function asyncUpdateFields(Request $request): array
    {
        $clientId = $request->get("clint_type");

        return [
            "showPhoneField" => $clientId === 1065, // Показываем поле, если выбран "Гость"
        ];
    }

    public function updatePhoneField(Request $request)
    {
        session(["showPhoneField" => $request->input("client") === "guest"]);
    }

    public function getAvailableItems()
    {
        $startDate = request()->input("orderItem.rent_start_date");
        $startTime = request()->input("orderItem.rent_start_time");
        $endDate = request()->input("orderItem.rent_end_date");
        $endTime = request()->input("orderItem.rent_end_time");

        $items = [];
        if ($startDate && $startTime && $endDate && $endTime) {
            $items = (new GoodService())->getAllAvailableItems(
                $startDate,
                $startTime,
                $endDate,
                $endTime,
            );
        }

        return $items;
    }

    // ✅ Метод, который теперь НЕ перезагружает страницу
    public function openAddItemModal()
    {
        return [];
    }

    // ✅ Добавление товара в список
    public function addItem(Request $request)
    {
        $data = $request->all();
        $globalDates = session()->get("quick_order_global_dates", []);

        $startDate = $data["orderItem"]["rent_start_date"] ?? $globalDates["rent_start_date"] ?? null;
        $startTime = $data["orderItem"]["rent_start_time"] ?? $globalDates["rent_start_time"] ?? null;
        $endDate = $data["orderItem"]["rent_end_date"] ?? $globalDates["rent_end_date"] ?? null;
        $endTime = $data["orderItem"]["rent_end_time"] ?? $globalDates["rent_end_time"] ?? null;

        if (!$startDate || !$startTime || !$endDate || !$endTime) {
            Toast::error("Укажите даты аренды!");
            return;
        }

        $itemId = $this->getAvailableItemsByTime(
            $data["orderItem"]["item_id"],
            $startDate,
            $startTime,
            $endDate,
            $endTime,
            $this->draftReservedItemIds($startDate, $startTime, $endDate, $endTime),
        );

        if (!$itemId) {
            Toast::error("Товар недоступен на это время!");
            return;
        }

        /**
         * @var Item $item
         */
        $item = Item::query()->where("id", $itemId)->first();

        $diffInDays = $this->rentDays($startDate, $startTime, $endDate, $endTime);

        $cost = $this->mainItemCost($item->good, $diffInDays);

        // В селекте дополнений — товары (Good). Бронируем конкретные свободные экземпляры,
        // исключая уже занятые черновиком и только что подобранный основной экземпляр.
        $excludeItemIds = array_merge(
            $this->draftReservedItemIds($startDate, $startTime, $endDate, $endTime),
            [$itemId],
        );
        $additionalItemIds = [];
        $addNames = [];
        foreach ($data["orderItem"]["additionals"] ?? [] as $additionalGoodId) {
            $additionalItemId = $this->getAvailableItemsByTime(
                $additionalGoodId,
                $startDate,
                $startTime,
                $endDate,
                $endTime,
                $excludeItemIds,
            );

            if (!$additionalItemId) {
                $additionalGood = Good::query()->find($additionalGoodId);
                Toast::error(
                    "Дополнение «" . ($additionalGood->name_ru ?? $additionalGoodId) . "» недоступно на это время!",
                );
                return;
            }

            $additional = Item::query()->with("good")->find($additionalItemId);
            $additionalItemIds[] = $additional->id;
            $excludeItemIds[] = $additional->id;
            $addNames[] = $additional->good->name_ru;
            $cost += $this->additionalItemCost($additional->good, $diffInDays);
        }

        $items = session()->get("quick_order_items", []);
        $items[] = [
            "id" => uniqid(),
            "rent_start_date" => $startDate,
            "rent_start_time" => $startTime,
            "rent_end_date" => $endDate,
            "rent_end_time" => $endTime,
            "item_id" => $itemId,
            "additionals" => $additionalItemIds,
            "is_additional" => $data["orderItem"]["is_additional"] ?? false,
            "name" => $item->good->name_ru,
            "add_names" => implode(", ", $addNames),
            "cost" => $cost,
        ];

        session()->put("quick_order_items", $items);

        Toast::info("Товар добавлен!");
    }

    // ✅ Удаление товара из списка
    public function removeItem(Request $request)
    {
        $items = array_filter(
            session()->get("quick_order_items", []),
            fn($item) => $item["id"] !== $request->input("id"),
        );
        session()->put("quick_order_items", array_values($items));

        Toast::info("Товар удалён!");
    }

    public function saveOrder(Request $request)
    {
        $requestOrderData = $request->input("order");
        $items = session()->get("quick_order_items", []);

        if (empty($items)) {
            Toast::error("Добавьте хотя бы один товар в заказ");
            return;
        }

        if (
            $requestOrderData["client_type"] == "client" &&
            ! Client::query()->find($requestOrderData["client_id"] ?? null)
        ) {
            Toast::error("Клиент не найден");
            return;
        }

        $goodItemService = new GoodItemService();

        [$order, $conflictNames] = DB::transaction(function () use ($requestOrderData, $items, $goodItemService) {
            // Блокируем экземпляры черновика до перепроверки — иначе параллельное оформление
            // с сайта успевает забронировать тот же экземпляр между проверкой и записью.
            $draftItemIds = [];
            foreach ($items as $item) {
                $draftItemIds[] = $item["item_id"];
                foreach ($item["additionals"] ?? [] as $additionalId) {
                    $draftItemIds[] = $additionalId;
                }
            }
            BookingLock::items($draftItemIds);

            // Черновик мог пролежать долго — за это время экземпляр могли занять другим заказом.
            // Сохранение не блокируем, но перечисляем занятое в уведомлении менеджеру.
            $conflictNames = [];
            foreach ($items as $item) {
                $draftRange = new TimeRange(
                    Carbon::parse($item["rent_start_date"] . " " . $item["rent_start_time"]),
                    Carbon::parse($item["rent_end_date"] . " " . $item["rent_end_time"]),
                );

                $checkIds = array_merge([$item["item_id"]], $item["additionals"] ?? []);

                foreach ($checkIds as $checkId) {
                    $checkItem = Item::query()->find($checkId);

                    if (
                        $checkItem &&
                        ! $goodItemService->isItemAvailableByTime($draftRange, $checkItem)
                    ) {
                        $conflictNames[] = $checkItem->name;
                    }
                }
            }

            if ($requestOrderData["client_type"] == "client") {
                $clientId = $requestOrderData["client_id"];
            } else {
                $id = random_int(1000000, 99999999);
                $client = Client::query()->create([
                    "name" => "Guest" . $id,
                    "phone" => $requestOrderData["phone"],
                    "instagram" => $requestOrderData["instagram"],
                    "discount" => 0,
                    "email" => "guest" . $id . "@mail.com",
                    "iin" => $id,
                    "confirmation_code" => "1111",
                    "email_confirmed" => 1,
                    "blocked" => 0,
                    "password" => bcrypt(\Illuminate\Support\Str::random(32)),
                ]);
                $clientId = $client->id;
            }

            $order = Order::query()->create([
                "status" => $requestOrderData["status"],
                "paid_status" => $requestOrderData["paid_status"],
                "amount_unpaid" => $requestOrderData["amount_unpaid"],
                "client_id" => $clientId,
            ]);

            $totalSum = 0;

            foreach ($items as $item) {
                $itemObj = Item::query()->find($item["item_id"])->load("good");

                $diffInDays = $this->rentDays(
                    $item["rent_start_date"],
                    $item["rent_start_time"],
                    $item["rent_end_date"],
                    $item["rent_end_time"],
                );

                $currentItemCost = $this->mainItemCost($itemObj->good, $diffInDays);

                $parentOrderItem = OrderItem::query()->create([
                    "item_id" => $itemObj->id,
                    "status" => $order->status,
                    "amount_of_days" => $diffInDays,
                    "order_id" => $order->id,
                    "is_additional" => $item["is_additional"] ? 1 : 0,
                    "additionals" => $item["additionals"] ?? [],
                    "amount_paid" => $currentItemCost,
                    "rent_start_date" => $item["rent_start_date"],
                    "rent_start_time" => $item["rent_start_time"],
                    "rent_end_date" => $item["rent_end_date"],
                    "rent_end_time" => $item["rent_end_time"],
                ]);

                foreach ($item["additionals"] ?? [] as $additionalId) {
                    $additional = Item::query()->find($additionalId)->load("good");

                    $additionalCost = $this->additionalItemCost($additional->good, $diffInDays);

                    $totalSum += $additionalCost;

                    OrderItem::query()->create([
                        "item_id" => $additional->id,
                        "order_id" => $order->id,
                        "parent_order_item_id" => $parentOrderItem->id,
                        "status" => "waiting",
                        "amount_of_days" => $diffInDays,
                        "is_additional" => true,
                        "additionals" => [],
                        "amount_paid" => $additionalCost,
                        "rent_start_date" => $item["rent_start_date"],
                        "rent_start_time" => $item["rent_start_time"],
                        "rent_end_date" => $item["rent_end_date"],
                        "rent_end_time" => $item["rent_end_time"],
                    ]);
                }

                $totalSum += $currentItemCost;
            }

            $order->amount_paid = $totalSum;
            $order->rent_end_date = $order->orderItems()->max("rent_end_date");
            $order->rent_start_date = $order->orderItems()->min("rent_start_date");
            $order->city_id = City::getPlatformCity();
            $order->manager_id = Auth::user()->id;
            $order->save();

            return [$order, array_values(array_unique($conflictNames))];
        });

        if ($requestOrderData["client_type"] == "client") {
            $aggreementFile = makeOrderAgreement(
                $order->fresh(["orderItems", "owner"]),
            );

            $order->attachment()->syncWithoutDetaching($aggreementFile->id);
            $order->agreement_id = $order->id;
            $order->save();
        }

        self::forgetDraft();

        if (count($conflictNames) != 0) {
            Toast::warning(
                "Заказ оформлен. Обратите внимание: за время оформления товар заняли — " .
                    implode(", ", $conflictNames),
            );
        } else {
            Toast::success("Заказ успешно оформлен!");
        }

        // Уходим со страницы черновика: повторное нажатие «Оформить» не должно создавать дубль.
        return redirect()->route("platform.orders.edit", $order);
    }

    /**
     * Сбросить черновик быстрого заказа. Вызывается при старте нового оформления
     * и после сохранения, чтобы позиции прошлого заказа не попали в следующий.
     */
    public static function forgetDraft(): void
    {
        session()->forget([
            "quick_order_items",
            "total_cost",
            "quick_order_global_dates",
        ]);
    }

    private function draftTotal(): int|float
    {
        return array_sum(array_column(session()->get("quick_order_items", []), "cost"));
    }

    /**
     * Количество суток аренды — по тому же правилу, что на сайте и в Api\OrderController:
     * неполные сутки округляются вверх, минимум одни.
     */
    private function rentDays($startDate, $startTime, $endDate, $endTime): int
    {
        $start = Carbon::parse("$startDate $startTime");
        $end = Carbon::parse("$endDate $endTime");

        return (int) max(1, ceil(($end->getTimestamp() - $start->getTimestamp()) / 86400));
    }

    private function mainItemCost(Good $good, int $days): int|float
    {
        return $days * ($good->getDiscountCost("platform") ?? $good->cost);
    }

    private function additionalItemCost(Good $good, int $days): int|float
    {
        $price = $good->additional_cost !== null && $good->additional_cost > 0
            ? $good->additional_cost
            : $good->cost;

        return $days * $price;
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

    /**
     * Экземпляры, уже занятые текущим черновиком заказа на пересекающийся период.
     * В базе их ещё нет, поэтому подбор свободного экземпляра обязан учитывать их отдельно,
     * иначе на каждое нажатие «добавить» возвращается один и тот же первый свободный экземпляр.
     *
     * @return int[]
     */
    private function draftReservedItemIds(
        $startDate,
        $startTime,
        $endDate,
        $endTime,
    ): array {
        $reserved = [];

        foreach (session()->get("quick_order_items", []) as $draftItem) {
            if (empty($draftItem["item_id"])) {
                continue;
            }

            if (
                ! $this->periodsOverlap(
                    $draftItem["rent_start_date"],
                    $draftItem["rent_start_time"],
                    $draftItem["rent_end_date"],
                    $draftItem["rent_end_time"],
                    $startDate,
                    $startTime,
                    $endDate,
                    $endTime,
                )
            ) {
                continue;
            }

            $reserved[] = (int) $draftItem["item_id"];

            foreach ($draftItem["additionals"] ?? [] as $additionalId) {
                $reserved[] = (int) $additionalId;
            }
        }

        return array_values(array_unique($reserved));
    }

    /**
     * Пересекаются ли два периода аренды. Границы считаются занятыми —
     * то же правило, что в SQL-проверке конфликтов.
     */
    private function periodsOverlap(
        $aStartDate,
        $aStartTime,
        $aEndDate,
        $aEndTime,
        $bStartDate,
        $bStartTime,
        $bEndDate,
        $bEndTime,
    ): bool {
        $aStart = Carbon::parse("$aStartDate $aStartTime");
        $aEnd = Carbon::parse("$aEndDate $aEndTime");
        $bStart = Carbon::parse("$bStartDate $bStartTime");
        $bEnd = Carbon::parse("$bEndDate $bEndTime");

        return $aStart <= $bEnd && $aEnd >= $bStart;
    }

    public function getAvailableItemsByTime(
        int $id,
        $startDate,
        $startTime,
        $endDate,
        $endTime,
        array $excludeItemIds = [],
    ) {
        $good = Good::query()->find($id);
        $cityId = City::getPlatformCity();

        $conflictingItemIds = DB::select(
            "
    SELECT order_items.item_id
    FROM order_items
    JOIN items ON order_items.item_id = items.id
    WHERE items.good_id = :good_id
    AND items.city_id = :city_id
    AND order_items.status IN ('in_rent', 'waiting', 'confirmed')
    AND (
        (order_items.rent_start_date < :end_date OR (order_items.rent_start_date = :end_date_v2 AND order_items.rent_start_time <= :end_time))
        AND
        (order_items.rent_end_date > :start_date OR (order_items.rent_end_date = :start_date_v2 AND order_items.rent_end_time >= :start_time))
    )
",
            [
                "good_id" => $good->id,
                "start_date" => $startDate,
                "start_date_v2" => $startDate,
                "start_time" => $startTime,
                "end_date" => $endDate,
                "end_date_v2" => $endDate,
                "end_time" => $endTime,
                "city_id" => $cityId,
            ],
        );
        $conflictingItemIds = array_map(function ($item) {
            return $item->item_id;
        }, $conflictingItemIds);

        $items = $good
            ->items()
            ->where("city_id", $cityId)
            ->where("status", "available")
            ->whereNotIn("id", $conflictingItemIds)
            ->whereNotIn("id", $excludeItemIds)
            ->with("good")
            ->get();

        foreach ($items as $item) {
            $item->good->name =
                $item->good["name_" . session()->get("locale", "ru")];
        }

        if ($items) {
            foreach ($items as $item) {
                return $item->id;
            }
        }

        return null;
    }
}
