<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Support\Arr;

class LikeFilter implements ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool
    {
        return stripos(
            Arr::get($item->toArray(), $condition->getComparatorField()),
            $condition->getComparatorValue()
        ) !== false;
    }
}
