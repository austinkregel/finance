<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class LessThanEqualFilter implements ConditionContract
{
    public function __invoke($items, ConditionalsContract $condition): bool
    {
        return $items[$condition->getComparatorField()] <= $condition->getComparatorValue();
    }
}
