<?php

namespace App\Orchid\Screens\Wanted;

use App\Models\Wanted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class WantedEditScreen extends Screen
{
    /**
     * @var Wanted
     */
    public $wanted;

    /**
     * Query data.
     */
    public function query(Wanted $wanted): array
    {
        $wanted->load('attachment');

        return [
            'wanted' => $wanted,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->wanted->exists ? __('translations.Edit wanted') : __('translations.Creating a new wanted');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Wanteds');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->wanted->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->wanted->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->wanted->exists),
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
                Input::make('wanted.name')
                    ->title(__('translations.Name'))
                    ->help(__('translations.Wanted name help'))
                    ->required(),

                Input::make('wanted.iin')
                    ->title(__('translations.Iin'))
                    ->help(__('translations.Wanted iin help'))
                    ->required(),

                Input::make('wanted.instagram')
                    ->title('Instagram')
                    ->help(__('translations.Wanted instagram help')),

                Upload::make('wanted.attachment')
                    ->title(__('translations.Pictures'))
                    ->help(__('translations.Wanted pictures help'))
                    ->acceptedFiles('image/*'),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function createOrUpdate(Wanted $wanted, Request $request)
    {
        $wanted->fill($request->except('wanted.attachment')['wanted']);
        $wanted->save();

        $wanted->attachment()->syncWithoutDetaching(
            $request->input('wanted.attachment', [])
        );

        Alert::info('You have successfully created a wanted.');

        return redirect()->route('platform.wanteds.list');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Wanted $wanted)
    {
        $wanted->delete();

        Alert::info('You have successfully deleted the wanted.');

        return redirect()->route('platform.wanteds.list');
    }
}
