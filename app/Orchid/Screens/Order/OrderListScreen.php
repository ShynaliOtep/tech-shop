<?php

namespace App\Orchid\Screens\Order;

use App\Models\Item;
use App\Models\Order;
use App\Orchid\Layouts\Order\OrderListLayout;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OrderListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): array
    {
        return [
            'orders' => Order::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return __('translations.Orders');
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('translations.Order');
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('translations.Create'))
                ->icon('pencil')
                ->route('platform.orders.create'),
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
            OrderListLayout::class,
        ];
    }

    public function cancel(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        $order->status = 'cancelled';
        $order->save();

        foreach($order->orderItems as $item){
            $item->status = 'cancelled';
            $item->save();
        }
        Toast::info(__('translations.Order was cancelled'));
    }

    public function return(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        $order->status = 'returned';
        $order->save();

        foreach($order->orderItems as $item){
            $item->status = 'returned';
            $item->save();
        }

        Toast::info(__('Order was returned'));
    }

    public function confirm(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        $order->status = 'in_rent';
        $order->save();
        foreach($order->orderItems as $item){
            $item->status = 'in_rent';
            $item->save();
        }
        Toast::info(__('Order was confirmed'));
    }

    public function makeAgreement(Request $request)
    {
        $order = Order::findOrFail($request->get('id'));

        $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

        $order->attachment()->syncWithoutDetaching($aggreementFile->id);
        $order->agreement_id = $aggreementFile->id;

        $order->save();
        Toast::info('Договор был успешно сформирован');
    }
}
