<?php

namespace App\Orchid\Screens\Order;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Models\Wanted;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrderEditScreen extends Screen
{
    /**
     * @var Order
     */
    public $order;

    /**
     * Query data.
     */
    public function query(Order $order): array
    {
        $order->load('orderItems', 'items');

        return [
            'order' => $order,
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return $this->order->exists ? 'Edit order' : 'Creating a new order';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Orders');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('translations.Create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(! $this->order->exists),

            Button::make(__('translations.Update'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->order->exists),

            Button::make(__('translations.Delete'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->order->exists),
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        $fields = [
            Layout::rows([
                Relation::make('order.client_id')
                    ->fromModel(Client::class, 'name')
                    ->help(__('translations.Order client help'))
                    ->required()
                    ->title(__('translations.Client')),

                Select::make('order.status')
                    ->options([
                        'returned' => __('translations.returned'),
                        'in_rent' => __('translations.in_rent'),
                        'waiting' => __('translations.waiting'),
                        'confirmed' => __('translations.confirmed'),
                        'cancelled' => __('translations.cancelled'),
                    ])
                    ->title(__('translations.Status'))
                    ->help(__('translations.Order status help')),

                Input::make('order.agreement_id')
                    ->title(__('translations.Agreement id'))
                    ->help(__('translations.Order agreement id help'))
                    ->type('number'),

                Select::make('order.paid_status')
                    ->options([
                        'pending' => __('Ожидается'),
                        'paid' => __('Оплачен'),
                        'unpaid' => __('Не оплачен'),
                    ])
                    ->title(__('Статус оплаты')),
                  //  ->help(__('translations.Order status help')),

                Input::make('order.unpaid_amount')
                    ->title(__('Не оплаченная сумма'))
                  //  ->help(__('translations.Order agreement id help')),
                    ->type('number'),

                Upload::make('order.attachment')
                    ->help(__('translations.Order Agreement help'))
                    ->title(__('translations.Agreement'))
                    ->acceptedFiles('.doc, .docx, .pdf, .txt'),

            ]),
        ];

        return $fields;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Order $order, Request $request)
    {
        $client = Client::query()->find($request->input('order.client_id'));

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            return redirect()->back()->withErrors(['authentication' => 'Клиент находится в списке подозреваемых']);
        }

        $order->fill($request->except('order.attachment')['order']);

        $order->amount_paid = 0;

        $order->save();

        $order->attachment()->syncWithoutDetaching(
            $request->input('order.attachment', [])
        );

        Alert::info('You have successfully created an order.');

        return redirect()->route('platform.orders.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function remove(Order $order)
    {
        $order->delete();

        Alert::info('You have successfully deleted the order.');

        return redirect()->route('platform.orders.list');
    }
}
