<?php

declare(strict_types=1);

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\LessThanFilter;
use Tests\TestCase;

class LessThanFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue): void
    {
        $condition = new Condition();
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new LessThanFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue,
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 310, 310],
            [false, 310, 312],
            [true, 500, 312],
            [true, 5, 1],
        ];
    }
}
