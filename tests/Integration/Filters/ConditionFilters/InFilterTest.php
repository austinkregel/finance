<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\InFilter;
use Tests\TestCase;

class InFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new InFilter();

        $this->assertSame($expect, $filter([
            'name' => $actualValue
        ], $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'Hello', [310]],
            [true, 310, [310]],
            [true, 'Yes', ['Yes']],
            [false, 'Yes', ['No']],
            [true, 'Yes', [4290 => 'Yes']],
        ];
    }
}
