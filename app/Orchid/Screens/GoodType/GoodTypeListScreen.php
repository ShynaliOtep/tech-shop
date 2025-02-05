<?php

namespace App\Orchid\Screens\GoodType;

use App\Models\GoodType;
use App\Orchid\Layouts\GoodType\GoodTypeListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class GoodTypeListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): array
    {
        return [
            'goodTypes' => GoodType::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return __('translations.GoodType');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.GoodTypes');
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
                ->route('platform.goodTypes.create'),
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
            GoodTypeListLayout::class,
        ];
    }
}
