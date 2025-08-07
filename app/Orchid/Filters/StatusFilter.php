<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class StatusFilter extends Filter
{
    public $parameters = [
        'status',
    ];

    public function name(): string
    {
        return 'Желіде';
    }

    public function run(Builder $builder): Builder
    {
        $status = $this->request->get('status');
        return $builder->where('order_items.status', $status);
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {

        return [
            Select::make('status')
                ->title('Статус')
                ->options([
                    null => __('translations.not chosen'),
                    'returned' => __('translations.returned'),
                    'in_rent' => __('translations.in_rent'),
                    'waiting' => __('translations.waiting'),
                    'confirmed' => __('translations.confirmed'),
                    'cancelled' => __('translations.cancelled'),
                ])
                ->empty('No select')
        ];
    }
}
