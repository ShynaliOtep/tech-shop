<?php

namespace App\Orchid\Screens\Good;

use App\Models\Good;
use App\Orchid\Layouts\Good\GoodListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

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
        ];
    }
}
