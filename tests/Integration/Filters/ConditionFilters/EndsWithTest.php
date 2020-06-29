<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Contracts\Models\ConditionalsContract;
use App\Filters\Conditions\EndsWith;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndsWithTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new EndsWith;

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'This', 'ThisIsAValue'],
            [false, 'Valu', 'ThisIsAValue'],
            [true, 'Value', 'ThisIsAValue'],
            [false, 'value', 'ThisIsAValUE'],// Ensure this is case sensitive
        ];
    }
}
