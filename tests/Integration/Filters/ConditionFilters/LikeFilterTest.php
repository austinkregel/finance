<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\LikeFilter;
use Tests\TestCase;

class LikeFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new LikeFilter();

        $this->assertSame($expect, $filter([
            'name' => $actualValue
        ], $condition));
    }

    public function dataProvider()
    {
        return [
            [true, "dog", "Hello dog, how are you?"],
            [false, "nope", "Hello dog, how are you?"],
        ];
    }
}
