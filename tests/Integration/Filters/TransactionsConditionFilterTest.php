<?php
declare(strict_types=1);

namespace Tests\Integration\Filters;

use App\Condition;
use App\Filters\TransactionsConditionFilter;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsConditionFilterTest extends TestCase
{
    use RefreshDatabase;

    public const SHOULD_BE_EMPTY = true;
    public const SHOULD_NOT_BE_EMPTY = false;

    public function testHandle(): void
    {
        $data = $this->dataProvider();

        foreach ($data as $index => [$expected, $expectedCount, $conditionable, $transactions]) {
            $filter = new TransactionsConditionFilter();

            $result = $filter->handle($conditionable, ...$transactions);

            $this->assertCount($expectedCount, $result);
            $this->assertSame($expected, empty($result), "Looking at you empty results [$index]");
        }
    }

    protected function dataProvider()
    {
        $tag = Tag::factory()->create();
        Condition::factory()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_LIKE,
            'value' => 'Hello',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag->id,
        ]);
        $tag->load('conditionals');

        $tag2 = Tag::factory()->create();
        Condition::factory()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_LIKE,
            'value' => 'netflix',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag2->id,
        ]);
        $tag2->load('conditionals');
        $tag3 = Tag::factory()->create(['name' => 'Subscriptions']);
        Condition::factory()->create([
            'parameter' => 'category',
            'comparator' => Condition::COMPARATOR_EQUAL,
            'value' => 'Subscription',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag3->id,
        ]);
        $tag3->load('conditionals');

        return [
            [
                self::SHOULD_BE_EMPTY,
                0,
                $tag,
                [Transaction::factory()->create([
                    'name' => 'EPC* GAMES STORE',
                ])
                ],
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3, // No conditions will default all things to be applied to the tag.
                Tag::factory()->create(),
                [
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Hello',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Nah dawg',
                    ]),
                ],
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3,
                $tag2,
                [
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Netflix',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Hello',
                    ]),
                    Transaction::factory()->create([
                        // A charge through a privacy.com card
                        'name' => 'PWP*Netflix.com          844-7718229  NY',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                    Transaction::factory()->create([
                        // A charge through a privacy.com card
                        'name' => 'GOG*netflix - GPay',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                ],
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3,
                $tag2,
                [
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Netflix',
                        'category_id' => $subscriptionCategory = Category::factory()->create([
                            'name' => 'Subscription',
                        ])->category_id,
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Hello',
                    ]),
                    Transaction::factory()->create([
                        // A charge through a privacy.com card
                        'name' => 'PWP*Netflix.com          844-7718229  NY',
                        'category_id' => $subscriptionCategory,
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                    Transaction::factory()->create([
                        // A charge through a privacy.com card
                        'name' => 'GOG*netflix - GPay',
                        'category_id' => $subscriptionCategory,
                    ]),
                    Transaction::factory()->create([
                        'name' => 'Nope',
                    ]),
                ],
            ],
        ];
    }
}
