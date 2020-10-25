<?php

namespace Tests\Integration\Filters\Conditions;

use App\Condition;
use App\Filters\Conditions\NotLikeFilter;
use Tests\TestCase;

class NotLikeFilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke($expect, $valueSearch, $actualValue): void
    {
        $condition = new Condition;
        $condition->parameter = 'name';
        $condition->value = $valueSearch;
        $filter = new NotLikeFilter();

        $this->assertSame($expect, $filter(collect([
            'name' => $actualValue
        ]), $condition));
    }

    public function dataProvider()
    {
        return [
            [false, 'dog', 'Hello dog, how are you?'],
            [true, 'nope', 'Hello dog, how are you?'],
        ];
    }
}
