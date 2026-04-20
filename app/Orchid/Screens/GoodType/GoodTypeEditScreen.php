<?php

namespace App\Orchid\Screens\GoodType;

use App\Models\GoodType;
use App\Models\Icon;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class GoodTypeEditScreen extends Screen
{
    /**
     * @var GoodType
     */
    public $goodType;

    /**
     * Query data.
     */
    public function query(GoodType $goodType): array
    {
        return [
            'goodType' => $goodType,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->goodType->exists ? __('translations.Edit goodType') : __('translations.Creating a new goodType');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.GoodTypes');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->goodType->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->goodType->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->goodType->exists),
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
                Input::make('goodType.name')
                    ->title(__('translations.Name'))
                    ->help(__('translations.GoodType name help'))
                    ->required(),

                Input::make('goodType.code')
                    ->title(__('translations.Route'))
                    ->required()
                    ->help(__('translations.GoodType code help')),

                Relation::make('goodType.icon')
                    ->fromModel(Icon::class, 'name') // показываем name
                    ->title('Иконка')
                    ->empty('Без иконки'),

                TextArea::make('goodType.description')
                    ->title(__('translations.Description'))
                    ->help(__('translations.GoodType description help'))
                    ->rows(3)
                    ->maxlength(200)
                    ->required(),
                Relation::make('goodType.parent_id')
                    ->fromModel(GoodType::class, 'name')
                    ->title('Родитель')
                    ->allowEmpty(),

            ]),
        ];
    }

    public function createOrUpdate(GoodType $goodType, Request $request)
    {
        $data = $request->input('goodType', []);

        if (empty($data['icon'])) {
            $data['icon'] = 'NULL'; // ⚠️ именно null, не 'NULL'
        }

        $goodType->fill($data)->save();

        Alert::info('You have successfully created a good type.');

        return redirect()->route('platform.goodTypes.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(GoodType $goodType)
    {
        $goodType->delete();

        Alert::info('You have successfully deleted the goodType.');

        return redirect()->route('platform.goodTypes.list');
    }
}
