<?php

namespace App\Orchid\Screens\GoodType;

use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;

class IconEditScreen extends Screen
{
    public $icon;

    public function query(Icon $icon): iterable
    {
        return [
            'icon' => $icon
        ];
    }

    public function name(): ?string
    {
        return $this->icon->exists ? 'Редактировать иконку' : 'Создать иконку';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->method('save')
                ->icon('check'),

            Button::make('Удалить')
                ->method('remove')
                ->icon('trash')
                ->canSee($this->icon->exists)
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('icon.name')
                    ->title('Название')
                    ->required(),

                Input::make('icon.upload')
                    ->type('file')
                    ->accept('.svg')
                    ->title('SVG файл')
            ])
        ];
    }

    public function save(Request $request, Icon $icon)
    {
        $request->validate([
            'icon.name' => 'required',
            'icon.upload' => $icon->exists ? 'nullable' : 'required|file|mimes:svg',
        ]);

        $data = $request->get('icon');

        $file = $request->file('icon.upload');

        if ($file) {
            $filename = trim($data['name']) . '.svg';

            $file->storeAs('icons', $filename, 'public');

            $data['file'] = $filename;
        }

        $icon->fill($data)->save();
        return redirect()->route('platform.icons');
    }

    public function remove(Icon $icon)
    {
        $icon->delete();
        return redirect()->route('platform.icons');
    }
}
