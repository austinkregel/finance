<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class EqualFilter implements ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool
    {
        if ($item instanceof Arrayable) {
            return $condition->getComparatorValue() == Arr::get($item->toArray(), $condition->getComparatorField());
        }

        return $condition->getComparatorValue() == $item[$condition->getComparatorField()];
    }
}
