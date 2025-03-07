<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\City;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        $cityId = session('selected_city', City::DEFAULT); // Получаем выбранный город
        $city = City::query()->find($cityId);
        return [
            Menu::make("Город: $city->name")
                ->icon('globe')
                ->route('platform.city'),
            Menu::make(__('translations.Get Started'))
                ->icon('bs.book')
                ->title(__('translations.Navigation'))
                ->route(config('platform.index')),

            Menu::make(__('translations.Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users'),

            Menu::make(__('translations.Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),

            Menu::make(__('translations.Goods'))
                ->icon('bs.box')
                ->route('platform.goods.list')
                ->permission('platform.goods.list'),

            Menu::make(__('translations.GoodTypes'))
                ->icon('bs.type')
                ->route('platform.goodTypes.list')
                ->permission('platform.goodTypes.list'),

            Menu::make(__('translations.Items'))
                ->icon('bs.archive')
                ->route('platform.items.list')
                ->permission('platform.items.list'),

            Menu::make(__('translations.Orders'))
                ->icon('bs.coin')
                ->route('platform.orders.list')
                ->permission('platform.orders.list'),

            Menu::make(__('translations.Clients'))
                ->icon('bs.file-earmark-person')
                ->route('platform.clients.list')
                ->permission('platform.clients.list'),

            Menu::make(__('translations.Wanteds'))
                ->icon('bs.incognito')
                ->route('platform.wanteds.list')
                ->permission('platform.wanteds.list'),

            Menu::make(__('translations.OrderItems'))
                ->icon('bs.bag-plus')
                ->route('platform.orderItems.list')
                ->permission('platform.orderItems.list'),
            Menu::make('Аналитика')
                ->icon('bar-chart')
                ->route('platform.analytics'),
            Menu::make('Заявки на вывод')
                ->icon('wallet')
                ->route('platform.withdraw.requests')


        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
