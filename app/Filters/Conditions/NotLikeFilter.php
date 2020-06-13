<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class NotLikeFilter implements ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool
    {
        return stripos($item[$condition->getComparatorField()], $condition->getComparatorValue()) === false;
    }
}
