<?php

namespace App\Orchid\Screens\Set;

use App\Models\Set;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\NumberRange;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SetListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'sets' => Set::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Наборы';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('translations.Create'))
                ->icon('pencil')
                ->route('platform.sets.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('sets', [
                TD::make('good.name_ru', __('translations.Name'))
                    ->sort()
                    ->filter(
                        Input::make()
                    )
                    ->render(function (Set $set) {
                        return Link::make($set->good->name_ru)
                            ->route('platform.sets.edit', $set);
                    }),
                TD::make('created_at', __('translations.Created'))
                    ->usingComponent(DateTimeSplit::class)
                    ->align(TD::ALIGN_RIGHT)
                    ->defaultHidden()
                    ->sort(),

                TD::make('updated_at', __('translations.Last edit'))
                    ->usingComponent(DateTimeSplit::class)
                    ->align(TD::ALIGN_RIGHT)
                    ->defaultHidden()
                    ->sort(),
            ])
        ];
    }
}
