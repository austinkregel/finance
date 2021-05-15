<?php
declare(strict_types=1);

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\LessThanEqualFilter;
use Tests\TestCase;

class LessThanEqualFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue): void
    {
        $condition = new Condition();
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new LessThanEqualFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue,
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [true, 310, 310],
            [false, 310, 312],
            [true, 312, 310],
            [false, false, true],
        ];
    }
}
