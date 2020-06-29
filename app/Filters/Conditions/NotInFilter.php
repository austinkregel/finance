<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class NotInFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return !in_array($condition->getComparatorValue(), Arr::get($item->toArray(), $condition->getComparatorField()));
    }
}
