<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EndsWith implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return Str::endsWith(Arr::get($item->toArray(), $condition->getComparatorField()), $condition->getComparatorValue()) ;
    }
}
