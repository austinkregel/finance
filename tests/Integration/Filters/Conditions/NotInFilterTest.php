<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Contracts\ConditionalsContract;
use App\Contracts\ConditionContract;
use App\Filters\Conditions\NotInFilter;
use Tests\TestCase;

class NotInFilterTest extends TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valuesWeWantOurTransactionToLookInto, $actualValue)
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valuesWeWantOurTransactionToLookInto;
        $filter = new NotInFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [true, 'Hello,Nope', 310],
            [false, 310, 310],
            [false, 'Yes,Nope', 'Yes'],
            [true, 'Yes,Nope', 'No'],
            [false, 'Yes,Ya',  'Yes'],
        ];
    }
}
