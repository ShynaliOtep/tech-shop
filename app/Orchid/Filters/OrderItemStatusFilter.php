<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class OrderItemStatusFilter extends Filter
{

    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'RAPE';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['status'];
    }

    public function run(Builder $builder): Builder
    {
        return $builder->where('status', '=', $this->request->get('status'));
    }

    public function display() : array
    {
        return [
            Select::make('status')
                ->options([
                    null => __('translations.not chosen'),
                    'returned' => __('translations.returned'),
                    'in_rent' => __('translations.in_rent'),
                    'waiting' => __('translations.waiting'),
                    'confirmed' => __('translations.confirmed'),
                    'cancelled' => __('translations.cancelled'),
                ])
                ->title(__('translations.Status'))
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        return $this->request->get('status');
    }
}
