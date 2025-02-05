<?php

namespace App\Orchid\Layouts\OrderItem;

use App\Orchid\Filters\OrderItemNameFilter;
use Orchid\Screen\Layouts\Selection;

class OrderItemFiltersLayout extends Selection
{
    public function filters(): array
    {
        return [
            OrderItemNameFilter::class,
        ];
    }
}
