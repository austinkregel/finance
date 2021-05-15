<?php

declare(strict_types=1);

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class LikeFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        $haystack = Arr::get($item->toArray(), $condition->getComparatorField()) ?? '';
        $needle = is_null($condition->getComparatorValue()) ? '' : $condition->getComparatorValue();

        return stripos($haystack, $needle) !== false;
    }
}
