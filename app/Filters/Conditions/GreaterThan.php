<?php

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class GreaterThan implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return floatval(Arr::get($item->toArray(), $condition->getComparatorField())) > floatval($condition->getComparatorValue());
    }
}
