<?php

namespace App\Orchid\Screens\Wanted;

use App\Models\Wanted;
use App\Orchid\Layouts\Wanted\WantedListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class WantedListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): array
    {
        return [
            'wanteds' => Wanted::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return __('translations.Wanted');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Wanteds');
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
                ->route('platform.wanteds.create'),
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
            WantedListLayout::class,
        ];
    }
}
