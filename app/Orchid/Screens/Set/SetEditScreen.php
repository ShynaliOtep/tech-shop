<?php

namespace App\Orchid\Screens\Set;

use App\Models\Good;
use App\Models\GoodType;
use App\Models\Set;
use App\Models\SetsGood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SetEditScreen extends Screen
{

    /**
     * @var Good
     */
    public $set;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Set $set): iterable
    {
        return [
            'set' => $set,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->set->exists ? __('Сеть редактировать') : __('Создать новый сеть');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->set->exists),
            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->set->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->set->exists),
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
            Layout::rows([
                Relation::make('set.good')
                    ->fromModel(Good::class, 'name_ru')
                    ->title('Указать товар')
                    ->help('Указать товар который хотите сделать набором'),

                Relation::make('set.goods.')
                    ->fromModel(Good::class, 'name_ru')
                    ->applyScope('simple')
                    ->multiple()
                    ->title('Добавить товар')
                    ->help('Добавить товары на этот набор'),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function createOrUpdate(Set $set, Request $request)
    {
        if ($set->exists && ($set->good_id != (int) $request->input('set.good'))) {
            $good = Good::find($set->good_id);
            $good->is_set = false;
            $good->save();
        }
        $set->good_id = $request->input('set.good');
        $set->save();
        $set->goods()->sync(request()->input('set.goods', []));

        $good = Good::find($request->input('set.good'));
        $good->is_set = true;
        $good->save();


        Alert::info('You have successfully created a good.');

        return redirect()->route('platform.sets.list');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Set $set)
    {
        $good = Good::find($set->good_id);
        $good->is_set = false;
        $good->save();
        $set->delete();

        Alert::info('You have successfully deleted the good.');

        return redirect()->route('platform.sets.list');
    }
}
