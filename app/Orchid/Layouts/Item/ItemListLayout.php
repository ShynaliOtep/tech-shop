<?php

namespace App\Orchid\Layouts\Item;

use App\Models\Good;
use App\Models\Item;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ItemListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'items';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('good_id', __('translations.Name'))
                ->sort()
                ->filter(
                    Relation::make()
                        ->fromModel(Good::class, 'name_'.session()->get('locale', 'ru'))
                )
                ->render(function (Item $item) {
                    return Link::make($item->good['name_'.session()->get('locale', 'ru')])
                        ->route('platform.items.edit', $item);
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
        ];
    }
}
