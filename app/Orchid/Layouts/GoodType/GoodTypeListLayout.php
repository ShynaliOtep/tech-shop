<?php

namespace App\Orchid\Layouts\GoodType;

use App\Models\GoodType;
use App\Models\Icon;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class GoodTypeListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'goodTypes';

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
                ->filter(Input::make())
                ->render(function (GoodType $goodType) {
                    return Link::make($goodType->name)
                        ->route('platform.goodTypes.edit', $goodType);
                }),

            TD::make('description', __('translations.Description'))
                ->sort()
                ->defaultHidden()
                ->width('250px')
                ->filter(Input::make())
                ->render(function (GoodType $goodType) {
                    return $goodType->description;
                }),

            TD::make('parent_id', 'Родитель')
                ->render(fn($c) => $c->parent?->name ?? '—'),


            TD::make('icon', __('translations.Icon'))
                ->render(function (GoodType $goodType) {
                    if (!$goodType->iconO) {
                        return '-';
                    }
                    $path = storage_path('app/public/icons/' . $goodType->iconO->file);

                    if (!file_exists($path)) {
                        return '-';
                    }

                    return file_get_contents($path);
                }),

            TD::make('code', __('translations.Route'))
                ->sort()
                ->filter(Input::make())
                ->render(function (GoodType $goodType) {
                    return $goodType->code;
                }),

            TD::make('created_at', __('Created'))
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
