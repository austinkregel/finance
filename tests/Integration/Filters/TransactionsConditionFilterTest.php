<?php

namespace Tests\Integration\Filters;

use App\Condition;
use App\Contracts\ConditionableContract;
use App\Filters\TransactionsConditionFilter;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsConditionFilterTest extends TestCase
{
    use RefreshDatabase;

    const SHOULD_BE_EMPTY = true;
    const SHOULD_NOT_BE_EMPTY = false;

    public function testHandle()
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
        $tag = factory(Tag::class)->create();
        factory(Condition::class)->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_LIKE,
            'value' => 'Hello',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag->id
        ]);
        $tag->load('conditionals');

        $tag2 = factory(Tag::class)->create();
        factory(Condition::class)->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_LIKE,
            'value' => 'netflix',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag2->id
        ]);
        $tag2->load('conditionals');
        $tag3 = factory(Tag::class)->create(['name' => 'Subscriptions']);
        factory(Condition::class)->create([
            'parameter' => 'category',
            'comparator' => Condition::COMPARATOR_EQUAL,
            'value' => 'Subscription',
            'conditionable_type' => Tag::class,
            'conditionable_id' => $tag3->id
        ]);
        $tag3->load('conditionals');

        return [
            [
                self::SHOULD_BE_EMPTY,
                0,
                $tag,
                [factory(Transaction::class)->create([
                    'name' => 'EPC* GAMES STORE',
                ])],
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3, // No conditions will default all things to be applied to the tag.
                factory(Tag::class)->create(),
                [
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Hello',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Nah dawg',
                    ]),
                ]
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3,
                $tag2,
                [
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Netflix',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Hello',
                    ]),
                    factory(Transaction::class)->create([
                        // A charge through a privacy.com card
                        'name' => 'PWP*Netflix.com          844-7718229  NY',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                    factory(Transaction::class)->create([
                        // A charge through a privacy.com card
                        'name' => 'GOG*netflix - GPay',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                ]
            ],
            [
                self::SHOULD_NOT_BE_EMPTY,
                3,
                $tag2,
                [
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Netflix',
                        'category_id' => $subscriptionCategory = factory(Category::class)->create([
                            'name' => 'Subscription'
                        ])->category_id
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Hello',
                    ]),
                    factory(Transaction::class)->create([
                        // A charge through a privacy.com card
                        'name' => 'PWP*Netflix.com          844-7718229  NY',
                        'category_id' => $subscriptionCategory
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                    factory(Transaction::class)->create([
                        // A charge through a privacy.com card
                        'name' => 'GOG*netflix - GPay',
                        'category_id' => $subscriptionCategory,
                    ]),
                    factory(Transaction::class)->create([
                        'name' => 'Nope',
                    ]),
                ]
            ],
        ];
    }
}
