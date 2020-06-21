<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\NotInLikeFilter;
use Tests\TestCase;

class NotInLikeFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new NotInLikeFilter();

        $this->assertSame($expect, $filter([[
            'name' => $actualValue
        ]], $condition));
    }

    public function dataProvider()
    {
        return [
            [true, "dog", "Hello dog, how are you?", "nope"],
            [false, "help", "nope"],
        ];
    }
}
