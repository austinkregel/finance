<?php

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;

class InLikeFilter implements ConditionContract
{
    public function __invoke(Arrayable $items, ConditionalsContract $condition): bool
    {
        foreach ($items->toArray() as $value) {
            if (stripos($value[$condition->getComparatorField()], $condition->getComparatorValue()) !== false) {
                return true;
            }
        }

        return false;
    }
}
