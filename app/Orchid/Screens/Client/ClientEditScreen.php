<?php

namespace App\Orchid\Screens\Client;

use App\Mail\ConfirmationMail;
use App\Models\Client;
use App\Models\Wanted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ClientEditScreen extends Screen
{
    /**
     * @var Client
     */
    public $client;

    /**
     * Query data.
     */
    public function query(Client $client): array
    {
        $client->load('attachment');

        return [
            'client' => $client,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->client->exists ? __('translations.Edit client') : __('translations.Creating a new client');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Clients');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->client->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->client->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->client->exists),
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
                Input::make('client.name')
                    ->title(__('translations.Name'))
                    ->help(__('translations.Client name help'))
                    ->required(),

                Input::make('client.email')
                    ->title(__('translations.Email'))
                    ->help(__('translations.Client email help'))
                    ->required(),

                Input::make('client.discount')
                    ->title(__('translations.Discount'))
                    ->help(__('translations.Client discount help'))
                    ->type('number')
                    ->required(),

                Input::make('client.iin')
                    ->title(__('translations.Iin'))
                    ->help(__('translations.Client iin help'))
                    ->required(),

                Input::make('client.phone')
                    ->title(__('translations.Phone'))
                    ->help(__('translations.Client phone help'))
                    ->required(),

                Select::make('client.email_confirmed')
                    ->options([
                        1 => __('translations.Confirmed'),
                        0 => __('translations.Not confirmed'),
                    ])
                    ->title(__('translations.Email confirmed'))
                    ->help(__('translations.Client email confirmed help'))
                    ->required(),

                Select::make('client.blocked')
                    ->options([
                        1 => __('translations.Blocked'),
                        0 => __('translations.Not blocked'),
                    ])
                    ->title(__('translations.Blocked'))
                    ->help(__('translations.Client blocked help'))
                    ->required(),

                Input::make('client.instagram')
                    ->title('Instagram')
                    ->help(__('translations.Client instagram help'))
                    ->required(),

                Input::make('client.password1')
                    ->title(__('translations.Password'))
                    ->help(__('translations.Client password help')),

                Upload::make('client.attachment')
                    ->title(__('translations.ID card'))
                    ->groups('idCards')
                    ->help(__('translations.Client id card help'))
                    ->acceptedFiles('image/*'),

                Upload::make('client.attachment')
                    ->title(__('translations.Client signature file'))
                    ->groups('signatures')
                    ->help(__('translations.Client signature file help'))
                    ->acceptedFiles('.pdf'),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function createOrUpdate(Client $client, Request $request)
    {
        $client->fill($request->except('client.attachment', 'client.password1')['client']);

        $client->confirmation_code = Str::random(10);
        if (($request->input('client')['password1'])) {
            $client->password = Hash::make(($request->input('client')['password1']));
        }

        if (! $client->exists || ! $client->email_confirmed) {
            Mail::to($client->email)->send(new ConfirmationMail($client->email, $client->confirmation_code));
        }
        $client->save();

        $client->attachment()->syncWithoutDetaching(
            $request->input('client.attachment', [])
        );

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            return redirect()->back()->withErrors(['authentication' => 'Клиент находится в списке подозреваемых']);
        }

        Alert::info('You have successfully created a client.');

        return redirect()->route('platform.clients.list');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Client $client)
    {
        $client->delete();

        Alert::info('You have successfully deleted the client.');

        return redirect()->route('platform.clients.list');
    }
}
