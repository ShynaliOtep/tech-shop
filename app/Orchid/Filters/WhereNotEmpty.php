<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\BaseHttpEloquentFilter;

class WhereNotEmpty extends BaseHttpEloquentFilter
{
    public function run(Builder $builder): Builder
    {
        return $builder->where($this->column, $this->getHttpValue());
    }

    public function isApply(): bool
    {
        return parent::isApply() && filled($this->getHttpValue());
    }
}
