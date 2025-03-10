<?php

declare(strict_types=1);

use App\Orchid\Screens\CitySelectorScreen;
use App\Orchid\Screens\Client\ClientEditScreen;
use App\Orchid\Screens\Client\ClientListScreen;
use App\Orchid\Screens\DashboardScreen;
use App\Orchid\Screens\Good\GoodEditScreen;
use App\Orchid\Screens\Good\GoodListScreen;
use App\Orchid\Screens\GoodType\GoodTypeEditScreen;
use App\Orchid\Screens\GoodType\GoodTypeListScreen;
use App\Orchid\Screens\Item\ItemEditScreen;
use App\Orchid\Screens\Item\ItemListScreen;
use App\Orchid\Screens\Order\OrderEditScreen;
use App\Orchid\Screens\Order\OrderListScreen;
use App\Orchid\Screens\OrderItem\OrderItemEditScreen;
use App\Orchid\Screens\OrderItem\OrderItemListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\QuickOrderScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\Wanted\WantedEditScreen;
use App\Orchid\Screens\Wanted\WantedListScreen;
use App\Orchid\Screens\WithdrawRequestScreen;
use App\Orchid\Screens\WithdrawRequestViewScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/


// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('translations.Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('translations.Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Roles'), route('platform.systems.roles')));

// Platform > System > Goods > Good
Route::screen('goods/{good}/edit', GoodEditScreen::class)
    ->name('platform.goods.edit')
    ->breadcrumbs(fn (Trail $trail, $good) => $trail
        ->parent('platform.goods.list')
        ->push($good['name_'.session()->get('locale', 'ru')], route('platform.goods.edit', $good)));

// Platform > System > Goods > Create
Route::screen('goods/create', GoodEditScreen::class)
    ->name('platform.goods.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.goods.list')
        ->push(__('translations.Create'), route('platform.goods.create')));

// Platform > System > Goods
Route::screen('goods', GoodListScreen::class)
    ->name('platform.goods.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Goods'), route('platform.goods.list')));

// Platform > System > GoodTypes > GoodType
Route::screen('good-types/{goodType}/edit', GoodTypeEditScreen::class)
    ->name('platform.goodTypes.edit')
    ->breadcrumbs(fn (Trail $trail, $goodType) => $trail
        ->parent('platform.goodTypes.list')
        ->push($goodType->name, route('platform.goodTypes.edit', $goodType)));

// Platform > System > GoodTypes > Create
Route::screen('good-types/create', GoodTypeEditScreen::class)
    ->name('platform.goodTypes.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.goodTypes.list')
        ->push(__('translations.Create'), route('platform.goodTypes.create')));

// Platform > System > Goods
Route::screen('good-types', GoodTypeListScreen::class)
    ->name('platform.goodTypes.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.GoodTypes'), route('platform.goodTypes.list')));

// Platform > System > Items > Item
Route::screen('items/{item}/edit', ItemEditScreen::class)
    ->name('platform.items.edit')
    ->breadcrumbs(fn (Trail $trail, $item) => $trail
        ->parent('platform.items.list')
        ->push($item->good['name_'.session()->get('locale', 'ru')], route('platform.items.edit', $item)));

// Platform > System > Items > Item
Route::screen('items/create', ItemEditScreen::class)
    ->name('platform.items.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.items.list')
        ->push(__('translations.Create'), route('platform.items.create')));

// Platform > System > Items
Route::screen('items', ItemListScreen::class)
    ->name('platform.items.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Items'), route('platform.items.list')));

// Platform > System > Orders > Order
Route::screen('orders/{order}/edit', OrderEditScreen::class)
    ->name('platform.orders.edit')
    ->breadcrumbs(fn (Trail $trail, $order) => $trail
        ->parent('platform.orders.list')
        ->push('Order #    '.$order->id, route('platform.orders.edit', $order)));

// Platform > System > Orders > Order
Route::screen('orders/create', OrderEditScreen::class)
    ->name('platform.orders.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.orders.list')
        ->push(__('translations.Create'), route('platform.orders.create')));

// Platform > System > Orders
Route::screen('orders', OrderListScreen::class)
    ->name('platform.orders.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('translations.Orders'), route('platform.orders.list')));

// Platform > System > clients > client
Route::screen('clients/{client}/edit', ClientEditScreen::class)
    ->name('platform.clients.edit')
    ->breadcrumbs(fn (Trail $trail, $client) => $trail
        ->parent('platform.clients.list')
        ->push('client #    '.$client->id, route('platform.clients.edit', $client)));

// Platform > System > clients > client
Route::screen('clients/create', ClientEditScreen::class)
    ->name('platform.clients.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.clients.list')
        ->push(__('translations.Create'), route('platform.clients.create')));

// Platform > System > clients
Route::screen('clients', ClientListScreen::class)
    ->name('platform.clients.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('clients'), route('platform.clients.list')));

// Platform > System > wanteds > wanted
Route::screen('wanteds/{wanted}/edit', WantedEditScreen::class)
    ->name('platform.wanteds.edit')
    ->breadcrumbs(fn (Trail $trail, $wanted) => $trail
        ->parent('platform.wanteds.list')
        ->push('wanted #    '.$wanted->id, route('platform.wanteds.edit', $wanted)));

// Platform > System > wanteds > wanted
Route::screen('wanteds/create', WantedEditScreen::class)
    ->name('platform.wanteds.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.wanteds.list')
        ->push(__('translations.Create'), route('platform.wanteds.create')));

// Platform > System > wanteds
Route::screen('wanteds', WantedListScreen::class)
    ->name('platform.wanteds.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('wanteds'), route('platform.wanteds.list')));

// Platform > System > orderItems > orderItem
Route::screen('orderItems/{orderItem}/edit', OrderItemEditScreen::class)
    ->name('platform.orderItems.edit')
    ->breadcrumbs(fn (Trail $trail, $orderItem) => $trail
        ->parent('platform.orderItems.list')
        ->push('orderItem #    '.$orderItem->id, route('platform.orderItems.edit', $orderItem)));

// Platform > System > orderItems > orderItem
Route::screen('orderItems/create', OrderItemEditScreen::class)
    ->name('platform.orderItems.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.orderItems.list')
        ->push(__('Create'), route('platform.orderItems.create')));

// Platform > System > orderItems
Route::screen('orderItems', OrderItemListScreen::class)
    ->name('platform.orderItems.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('orderItems'), route('platform.orderItems.list')));
Route::screen('analytics', DashboardScreen::class)->name('platform.analytics');

Route::screen('/withdraw-requests', WithdrawRequestScreen::class)
    ->name('platform.withdraw.requests');

Route::screen('/withdraw-request/{id}', WithdrawRequestViewScreen::class)
    ->name('platform.withdraw.view');

Route::screen('city', CitySelectorScreen::class)->name('platform.city');

Route::screen('quick-order', QuickOrderScreen::class)
    ->name('platform.quick-order');
//    ->breadcrumbs(fn ($trail) => $trail->parent('platform.main')->push('Быстрое оформление заказа'));

