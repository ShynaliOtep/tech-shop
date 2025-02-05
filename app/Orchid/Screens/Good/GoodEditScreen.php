<?php

namespace App\Orchid\Screens\Good;

use App\Models\Good;
use App\Models\GoodType;
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

class GoodEditScreen extends Screen
{
    /**
     * @var Good
     */
    public $good;

    /**
     * Query data.
     */
    public function query(Good $good): array
    {
        $good->load('attachment');

        return [
            'good' => $good,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->good->exists ? __('translations.Edit good') : __('translations.Creating a new good');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Goods');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->good->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->good->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->good->exists),
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
            Layout::rows([
                Input::make('good.name_ru')
                    ->title(__('translations.Name ru'))
                    ->help(__('translations.Good name help'))
                    ->required(),

                Input::make('good.name_en')
                    ->title(__('translations.Name en'))
                    ->help(__('translations.Good name help'))
                    ->required(),

                Input::make('good.cost')
                    ->title(__('translations.Cost'))
                    ->help(__('translations.Good cost help'))
                    ->type('number')
                    ->required(),

                Input::make('good.discount_cost')
                    ->title(__('translations.Discount cost'))
                    ->help(__('translations.Good discount_cost help'))
                    ->type('number'),

                Input::make('good.damage_cost')
                    ->title(__('translations.Damage cost'))
                    ->help(__('translations.Good damage_cost help'))
                    ->type('number')
                    ->required(),

                Input::make('good.additional_cost')
                    ->title(__('translations.Additional cost'))
                    ->help(__('translations.Good additional_cost help'))
                    ->type('number')
                    ->required(),

                Relation::make('good.good_type_id')
                    ->fromModel(GoodType::class, 'name')
                    ->help(__('translations.Good goodType help'))
                    ->required()
                    ->title(__('translations.GoodType')),

                Relation::make('good.related_goods')
                    ->fromModel(Good::class, 'name_'.session()->get('locale', 'ru'))
                    ->help(__('translations.Good related_goods help'))
                    ->multiple()
                    ->title(__('translations.Related goods')),

                Relation::make('good.additionals')
                    ->fromModel(Good::class, 'name_'.session()->get('locale', 'ru'))
                    ->help(__('translations.Good additionals help'))
                    ->multiple()
                    ->title(__('translations.Additional')),

                TextArea::make('good.description_ru')
                    ->title(__('translations.Description ru'))
                    ->help(__('translations.Good description help'))
                    ->rows(3)
                    ->maxlength(200)
                    ->required(),

                TextArea::make('good.description_en')
                    ->title(__('translations.Description en'))
                    ->help(__('translations.Good description help'))
                    ->rows(3)
                    ->maxlength(200)
                    ->required(),

                Input::make('good.priority')
                    ->title(__('translations.Good priority'))
                    ->help(__('translations.Good priority help'))
                    ->type('number'),

                Upload::make('good.attachment')
                    ->help(__('translations.Good attachment help'))
                    ->title(__('translations.Images'))
                    ->acceptedFiles('image/*'),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function createOrUpdate(Good $good, Request $request)
    {
        $good->fill($request->except('good.attachment')['good']);

        if (! $request->input('good.related_goods')) {
            $good->related_goods = '[]';
        }

        if (! $request->input('good.additionals')) {
            $good->additionals = '[]';
        }

        $good->save();

        $good->attachment()->syncWithoutDetaching(
            $request->input('good.attachment', [])
        );

        Alert::info('You have successfully created a good.');

        return redirect()->route('platform.goods.list');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Good $good)
    {
        $good->delete();

        Alert::info('You have successfully deleted the good.');

        return redirect()->route('platform.goods.list');
    }
}
