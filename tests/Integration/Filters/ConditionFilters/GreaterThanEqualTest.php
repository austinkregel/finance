<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\GreaterThanEqual;
use Tests\TestCase;

class GreaterThanEqualTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new GreaterThanEqual();

        $this->assertSame($expect, $filter([
            'name' => $actualValue
        ], $condition));
    }

    public function dataProvider()
    {
        return [
            [true, 310, 310],
            [false, 500, 312],
            [true, 312, 500],
        ];
    }
}
