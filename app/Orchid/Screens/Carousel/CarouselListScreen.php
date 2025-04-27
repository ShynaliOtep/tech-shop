<?php

namespace App\Orchid\Screens\Carousel;

use App\Models\Carousel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CarouselListScreen extends Screen
{
    public function query(): iterable
    {
        return ['carousels' => Carousel::orderBy('order')->paginate()];
    }

    public function name(): ?string
    {
        return 'Карусель';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')->route('platform.carousel.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('carousels', [
                TD::make('id', 'ID')->sort(),
                TD::make('image', 'Изображение')->render(fn($carousel) => "<img src='{$carousel->image}' width='100'>"
                ),
                TD::make('title', 'Заголовка')
                    ->render(fn($carousel) => "<div style='white-space: pre-line; font-size: 14px; color: #333;'>" . e(strip_tags($carousel->text)) . "</div>")
                    ->width('300px'),
                TD::make('order', 'Порядок')->sort(),
                TD::make('actions', 'Действия')->render(function ($carousel) {
                    return
                        Link::make('Редактировать')
                            ->route('platform.carousel.edit', $carousel->id)
                            ->icon('pencil')
                            ->class('btn btn-sm btn-primary me-2') .
                        Button::make('Удалить')
                            ->method('remove')
                            ->parameters(['id' => $carousel->id])
                            ->icon('trash')
                            ->confirm('Вы уверены, что хотите удалить этот элемент?')
                            ->class('btn btn-sm btn-danger');
                }),
            ]),
        ];
    }

    public function remove($id)
    {
        $carousel = Carousel::findOrFail($id);
        $carousel->delete();

        Toast::info('Элемент удалён');
        return redirect()->route('platform.carousel.list');
    }
}
