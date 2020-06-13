<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class GreaterThanEqual implements ConditionContract
{
    public function __invoke($items, ConditionalsContract $condition): bool
    {
        return $items[$condition->getComparatorField()] >= $condition->getComparatorValue();
    }
}
