<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;

class InLikeFilter implements ConditionContract
{
    public function __invoke($items, ConditionalsContract $condition): bool
    {
        foreach ($items as $value) {
            if (stripos($value[$condition->getComparatorField()], $condition->getComparatorValue()) !== false) {
                return true;
            }
        }

        return false;
    }
}
