<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\InLikeFilter;
use Tests\TestCase;

class InLikeFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new InLikeFilter();

        $this->assertSame($expect, $filter([[
            'name' => $actualValue
        ]], $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'I', 'TEAM'],
            [true, 'ris', 'surprising'],
        ];
    }
}
