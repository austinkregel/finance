<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class NotEqualFilter implements ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool
    {
        return $condition->getComparatorValue() != $item[$condition->getComparatorField()];
    }
}
