<?php
declare(strict_types=1);

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\EqualFilter;
use Tests\TestCase;

class EqualFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue): void
    {
        $condition = new Condition();
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new EqualFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue,
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'This', 'ThisIsAValue'],
            [false, 'Valu', 'ThisIsAValue'],
            [false, 'Value', 'ThisIsAValue'],
            [false, 'value', 'ThisIsAValUE'],
            [false, 'shouldbesame', 'ShouldBeSame'],

            [true, 'ShouldBeSame', 'ShouldBeSame'],

            [false, true, false], // Ensure this works with types
            [true, '12', 12], // Make it loose types and not strict.
            [true, 12, '12'], // Make it loose types and not strict.
            [true, 310, 310],
        ];
    }
}
