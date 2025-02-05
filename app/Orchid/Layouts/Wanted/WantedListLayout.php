<?php

namespace App\Orchid\Layouts\Wanted;

use App\Models\Wanted;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class WantedListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'wanteds';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', __('translations.Name'))
                ->sort()
                ->filter(
                    Input::make()
                )
                ->render(function (Wanted $wanted) {
                    return Link::make($wanted->name)
                        ->route('platform.wanteds.edit', $wanted);
                }),

            TD::make('iin', __('translations.Iin'))
                ->sort()
                ->filter(
                    Input::make()
                ),

            TD::make('instagram', 'Instagram')
                ->sort()
                ->filter(
                    Input::make()
                ),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class, tz: 'UTC')
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('translations.Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),
        ];
    }
}
