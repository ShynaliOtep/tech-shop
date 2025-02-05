<?php

namespace App\Orchid\Layouts\Client;

use App\Models\Client;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ClientListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'clients';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', __('translations.Name'))
                ->sort()
                ->filter(
                    Input::make()
                )
                ->render(function (Client $client) {
                    return Link::make($client->name)
                        ->route('platform.clients.edit', $client);
                }),

            TD::make('phone', __('translations.Phone'))
                ->sort()
                ->filter(
                    Input::make()
                ),

            TD::make('discount', __('translations.Discount'))
                ->sort()
                ->filter(
                    Input::make()
                ),

            TD::make('instagram', 'Instagram')
                ->sort()
                ->filter(
                    Input::make()
                ),

            TD::make('email_confirmed', __('translations.Email confirmed'))
                ->sort()
                ->filter(
                    Select::make('email_confirmed')
                        ->options([
                            null => __('translations.not chosen'),
                            1 => __('translations.Confirmed'),
                            0 => __('translations.Not confirmed'),
                        ])
                        ->title('email_confirmed')
                )->render(function (Client $client) {
                    return [
                        1 => __('translations.Confirmed'),
                        0 => __('translations.Not confirmed'),
                    ][$client->email_confirmed];
                }),

            TD::make('blocked', __('translations.Blocked'))
                ->sort()
                ->filter(
                    Select::make('email_confirmed')
                        ->options([
                            null => __('translations.not chosen'),
                            1 => __('translations.Blocked'),
                            0 => __('translations.Not blocked'),
                        ])
                        ->title('email_confirmed')
                )->render(function (Client $client) {
                    return [
                        1 => __('translations.Blocked'),
                        0 => __('translations.Not blocked'),
                    ][$client->blocked];
                }),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class, tz: 'UTC')
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('translations.Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (\App\Models\Client $client) {
                    $btnsList = [
                        Link::make(__('translations.Look at orders'))
                            ->route('platform.orders.list',
                                [
                                    'filter[client_id]' => $client->id,
                                ]
                            )
                            ->icon('bs.search'),
                    ];

                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list($btnsList);
                }),
        ];
    }
}
