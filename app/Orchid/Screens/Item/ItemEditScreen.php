<?php

namespace App\Orchid\Screens\Item;

use App\Models\Good;
use App\Models\Item;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ItemEditScreen extends Screen
{
    /**
     * @var Item
     */
    public $item;

    /**
     * Query data.
     */
    public function query(Item $item): array
    {
        return [
            'item' => $item,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->item->exists ? __('translations.Edit item') : __('translations.Creating a new item');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Items');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->item->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->item->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->item->exists),
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

                Relation::make('item.good_id')
                    ->fromModel(Good::class, 'name_'.session()->get('locale', 'ru'))
                    ->help(__('translations.Item good help'))
                    ->title(__('translations.Good')),

                Input::make('item.serial')
                    ->title(__('translations.ItemSerial'))
                    ->help(__('translations.ItemSerialHelp'))
                    ->type('string'),
            ]),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Item $item, Request $request)
    {
        $item->fill($request->get('item'))->save();

        Alert::info('You have successfully created am item.');

        return redirect()->route('platform.items.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Item $item)
    {
        $item->delete();

        Alert::info('You have successfully deleted the item.');

        return redirect()->route('platform.items.list');
    }
}
