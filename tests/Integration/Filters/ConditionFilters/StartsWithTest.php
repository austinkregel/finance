<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\StartsWith;
use Tests\TestCase;

class StartsWithTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new StartsWith();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [true, "Hello dog", "Hello dog, how are you?"],
            [false, "nope", "Hello dog, how are you?"],
            [false, "ello", "Hello dog, how are you?"],
        ];
    }
}
