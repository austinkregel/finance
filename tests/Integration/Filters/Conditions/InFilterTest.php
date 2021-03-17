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
    public function testInvoke($expect, $valuesWeWantOurTransactionToLookInto, $actualValue): void
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valuesWeWantOurTransactionToLookInto;
        $filter = new InFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue,
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'Hello,Nope', 310],
            [true, 310, 310],
            [true, 'Yes,Nope', 'Yes'],
            [false, 'Yes,Nope', 'No'],
            [true, 'Yes,Ya',  'Yes'],
        ];
    }
}
