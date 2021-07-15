<?php
declare(strict_types=1);

namespace App\Filters\Conditions;

use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class NotInFilter implements ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool
    {
        $arrayOfData = explode(',', $condition->getComparatorValue());

        return ! in_array(Arr::get($item->toArray(), $condition->getComparatorField()), array_map('trim', $arrayOfData));
    }
}
