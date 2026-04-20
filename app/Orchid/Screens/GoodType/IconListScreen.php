<?php

namespace App\Orchid\Screens\GoodType;

use App\Models\Icon;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

class IconListScreen extends Screen
{
    public function name(): ?string
    {
        return 'Иконки';
    }

    public function query(): iterable
    {
        return [
            'icons' => Icon::paginate()
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('plus')
                ->route('platform.icons.create')
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('icons', [
                TD::make('id'),
                TD::make('name'),

                TD::make('file', 'Иконка')
                    ->render(function (Icon $icon) {
                        $path = storage_path('app/public/icons/' . $icon->file);

                        if (!file_exists($path)) {
                            return 'нет файла';
                        }

                        return file_get_contents($path);
                    }),

                TD::make('actions')
                    ->render(function (Icon $icon) {
                        return Link::make('Редактировать')
                            ->route('platform.icons.edit', $icon);
                    }),
            ])
        ];
    }
}
