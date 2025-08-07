<?php

namespace App\Orchid\Layouts\OrderItem;

use App\Orchid\Filters\OrderItemNameFilter;
use App\Orchid\Filters\StatusFilter;
use Orchid\Screen\Layouts\Selection;

class OrderItemFiltersLayout extends Selection
{
    public function filters(): array
    {
        return [
            OrderItemNameFilter::class,
            StatusFilter::class,
        ];
    }
}
