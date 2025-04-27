<?php

namespace App\Orchid\Layouts;

use Illuminate\Http\Request;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ItemSelectListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'rent_start_date',
        'rent_end_date',
        'rent_start_time',
        'rent_end_time',
    ];

    protected $asyncMethod = 'asyncGetOptions';

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    protected function layouts(): iterable
    {
        //dump($this->query->get('rent_start_date'));
        return [
            Layout::rows([
                Select::make('orderItem.item_id')
                    ->options([])
                    ->help(__('translations.OrderItem item help'))
                    ->required()
                    ->title(__('translations.Item'))
            ])
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        // TODO: Implement handle() method.
    }
}
