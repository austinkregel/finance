<?php

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class LikeFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return stripos(Arr::get($item->toArray(), $condition->getComparatorField()), $condition->getComparatorValue()) !== false;
    }
}
