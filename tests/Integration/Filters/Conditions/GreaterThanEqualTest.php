<?php
declare(strict_types=1);

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\GreaterThanEqual;
use Tests\TestCase;

class GreaterThanEqualTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue): void
    {
        $condition = new Condition();
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new GreaterThanEqual();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue,
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [true, 310, 310],
            [false, 500, 312],
            [true, 312, 500],
            [true, '312', '500'],
            [true, 312, '500'],
        ];
    }
}
