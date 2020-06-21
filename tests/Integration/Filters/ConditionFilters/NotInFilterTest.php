<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Contracts\Models\ConditionalsContract;
use App\Contracts\Models\ConditionContract;
use App\Filters\Conditions\NotInFilter;
use Tests\TestCase;

class NotInFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new NotInFilter();

        $this->assertSame($expect, $filter([
            'name' => $actualValue
        ], $condition));
    }

    public function dataProvider()
    {
        return [
            [true, "dog", ["Hello dog, how are you?", "nope"]],
            [false, "nope", ["nope", "dog"]],
        ];
    }
}
