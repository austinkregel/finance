<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\GreaterThan;
use Tests\TestCase;

class GreaterThanTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new GreaterThan();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 310, 310],
            [true, 310, 312],
            [true, '310', '312'],
            [true, 310, '312'],
            [true, false, true],
        ];
    }
}
