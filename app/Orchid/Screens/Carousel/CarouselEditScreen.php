<?php

namespace App\Orchid\Screens\Carousel;

use App\Models\Carousel;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CarouselEditScreen extends Screen
{
    public $carousel;

    public function query(Carousel $carousel): iterable
    {
        return ['carousel' => $carousel];
    }

    public function name(): ?string
    {
        return $this->carousel->exists ? 'Редактирование' : 'Создание';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')->route('platform.carousel.list'),
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Picture::make('carousel.image') // Используй Picture для загрузки изображения
                ->title('Изображение')
                    ->targetRelativeUrl(),

                Input::make('carousel.order')->title('Порядок'),
                Input::make('carousel.title')->title('Заголовка'),
                Input::make('carousel.short_text')->title('Краткое описание'),
                Quill::make('carousel.text')->title('Описание'),
            ]),
        ];
    }

    public function save(Request $request, Carousel $carousel)
    {
        $data = $request->all();

        $carousel->image = $data['carousel']['image'];
        $carousel->title = $data['carousel']['title'];
        $carousel->short_text = $data['carousel']['short_text'];
        $carousel->order = $data['carousel']['order'];
        $carousel->text = $data['carousel']['text'];
        $carousel->save();

        Toast::info('Карусель сохранена');
        return redirect()->route('platform.carousel.list');
    }

}
