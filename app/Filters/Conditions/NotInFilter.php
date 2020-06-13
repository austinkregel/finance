<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class NotInFilter implements ConditionContract
{
    public function __invoke($items, ConditionalsContract $condition): bool
    {
        return !in_array($condition->getComparatorValue(), $items[$condition->getComparatorField()]);
    }
}
