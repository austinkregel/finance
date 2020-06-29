<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;

class NotInLikeFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        foreach ($item->toArray() as $value) {
            if (stripos($value[$condition->getComparatorField()], $condition->getComparatorValue()) === false) {
                return false;
            }
        }

        return true;
    }
}
