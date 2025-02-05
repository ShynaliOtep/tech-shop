<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class OrderItemNameFilter extends Filter
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
        return ['id', 'name', 'status', 'amount_paid', 'rent_start_date', 'rent_end_date', 'rent_start_time', 'rent_end_time'];
    }

    public function run(Builder $builder): Builder
    {
        return $builder
            ->select('order_items.*', 'items.id as item_id', 'goods.id as good_id', 'goods.name_ru', 'goods.name_en')
            ->join('items', 'items.id', '=', 'order_items.item_id')
            ->join('goods', 'goods.id', '=', 'items.good_id')
            ->where(function($query) {
                $query->where('goods.name_ru', 'like', '%'.$this->request->get('name').'%')
                    ->orWhere('goods.name_en', 'like', '%'.$this->request->get('name').'%');
            });
    }

    public function display() : array
    {
        return [
            Input::make()
                ->empty()
                ->value($this->request->get('name'))
                ->help(__('translations.OrderItem item help'))
                ->title(__('translations.Item'))
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        return $this->request->get('name');
    }
}
