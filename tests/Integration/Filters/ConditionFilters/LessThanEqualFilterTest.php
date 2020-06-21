<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use App\Filters\Conditions\LessThanEqualFilter;
use Tests\TestCase;

class LessThanEqualFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new LessThanEqualFilter();

        $this->assertSame($expect, $filter([
            'name' => $actualValue
        ], $condition));
    }

    public function dataProvider()
    {
        return [
            [true, 310, 310],
            [false, 310, 312],
            [true, 312, 310],
            [false, false, true],
        ];
    }
}

