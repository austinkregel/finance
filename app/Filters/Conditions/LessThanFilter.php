<?php
declare(strict_types=1);

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class LessThanFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        return ((float) Arr::get($item->toArray(), $condition->getComparatorField())) < ((float) $condition->getComparatorValue());
    }
}
