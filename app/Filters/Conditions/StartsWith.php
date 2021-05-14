<?php

declare(strict_types=1);

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StartsWith implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return Str::startsWith(Arr::get($item->toArray(), $condition->getComparatorField()), $condition->getComparatorValue());
    }
}
