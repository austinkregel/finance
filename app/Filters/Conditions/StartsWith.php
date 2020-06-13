<?php

namespace App\Filters\Conditions;

use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use Illuminate\Support\Str;

class StartsWith implements ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool
    {
        return Str::startsWith($item[$condition->getComparatorField()], $condition->getComparatorValue()) ;
    }
}
