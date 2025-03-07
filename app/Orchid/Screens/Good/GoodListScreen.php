<?php

namespace App\Orchid\Screens\Good;

use App\Models\Good;
use App\Models\Item;
use App\Orchid\Layouts\Good\GoodListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class GoodListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): array
    {
        return [
            'goods' => Good::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return __('translations.Good');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Goods');
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('translations.Create'))
                ->icon('pencil')
                ->route('platform.goods.create'),
            ModalToggle::make('Применить скидку')
                ->modal('applyDiscountModal')
                ->icon('discount')
                ->method('applyDiscount')
                ->async('asyncGetSelectedProducts'),

        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            GoodListLayout::class,
            Layout::modal('applyDiscountModal', [
                Layout::rows([
                    Input::make('discount')
                        ->type('number')
                        ->title('Размер скидки (%)')
                        ->required()
                        ->min(0)
                        ->max(100),

                    Select::make('selected')
                        ->fromModel(Good::class, 'name_ru', 'id')
                        ->multiple()
                        ->searchable() // Добавляет поиск
                        ->title('Выберите товары')
                        ->required(),
                ]),
            ])->title('Применить скидку')->applyButton('Применить'),
        ];
    }


    public function applyDiscount(Request $request)
    {
        $discount = (int) $request->input('discount');
        $productIds = $request->input('selected', []);

        if (empty($productIds)) {
            Toast::warning('Выберите хотя бы один товар.');
            return;
        }

        // Получаем товары
        $goods = Good::whereIn('id', $productIds)->get();

        foreach ($goods as $good) {
            $discountCost = $good->cost - ($good->cost * ($discount / 100)); // Рассчитываем скидку
            $good->discount_cost = round($discountCost, 2); // Округляем до двух знаков
            $good->save();
        }

        Toast::success("Скидка {$discount}% успешно применена!");
    }



    public function asyncGetSelectedProducts(Request $request): array
    {
        return [
            'selected' => implode(',', $request->input('selected', [])),
        ];
    }

}
