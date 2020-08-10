<?php

namespace Tests\Integration\Listeners;

use App\Condition;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Listeners\ApplyGroupToTransactionAutomaticallyListener;
use App\Listeners\TriggerAlertIfConditionsPassListenerForTransaction;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class ApplyGroupToTransactionAutomaticallyListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleSuccess()
    {
        $this->expectsEvents([
            TransactionGroupedEvent::class
        ]);
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'bill',
            'user_id' => $transaction->account->owner->id
        ]);
        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessNotApplyBecauseTransactionIsFilteredOut()
    {

        $this->expectsEvents([]);
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'bill',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag->conditionals()->create([
            'parameter' => 'transaction.name',
            'comparator' => Condition::COMPARATOR_NOT_EQUAL,
            'value' => $transaction->name
        ]);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessNotApplyBecauseTransactionIsNotGettingDoubleTagged()
    {

        $this->expectsEvents([]);
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'bill',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag->conditionals()->create([
            'parameter' => 'transaction.name',
            'comparator' => Condition::COMPARATOR_NOT_EQUAL,
            'value' => $transaction->name
        ]);

        $transaction->tags()->attach($tag->id);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessLastAttachTag()
    {

        $this->expectsEvents([]);
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'bill',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag->conditionals()->create([
            'parameter' => 'transaction.name',
            'comparator' => Condition::COMPARATOR_EQUAL,
            'value' => $transaction->name
        ]);

        $transaction->tags()->attach($tag->id);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessAttachTagWithRelationInConditional()
    {
        $this->expectsEvents([
            TransactionGroupedEvent::class
        ]);
        $category = Category::first();
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'category_id' => $category->category_id,
        ]);
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'bill',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag->conditionals()->create([
            'parameter' => 'category.name',
            'comparator' => Condition::COMPARATOR_EQUAL,
            'value' => $category->name
        ]);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessAttachTagWhereAmountGreaterThan()
    {
        $this->expectsEvents([
            TransactionGroupedEvent::class
        ]);
        $category = Category::first();
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'name' => 'Netflix',
            'category_id' => $category->category_id,
            'amount' => 9.99
        ]);
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'debits/withdrawals',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_GREATER_THAN,
            'value' => '0'
        ]);
        $tag->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'fee'
        ]);
        $tag->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'transfer'
        ]);

        /** @var Tag $tag */
        $tag2 = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'credits/income',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_LESS_THAN,
            'value' => '0'
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'fee'
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'transfer'
        ]);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }

    public function testHandleSuccessAttachTagWithAllConditionsPassSetToFalse()
    {
        $this->expectsEvents([
            TransactionGroupedEvent::class
        ]);
        $category = Category::first();
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'name' => 'Netflix',
            'category_id' => $category->category_id,
            'amount' => 9.99
        ]);
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'debits/withdrawals',
            'user_id' => $transaction->account->owner->id,
            'must_all_conditions_pass' => false,
        ]);
        $tag->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_GREATER_THAN,
            'value' => '0'
        ]);
        $tag->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'fee'
        ]);
        $tag->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'transfer'
        ]);

        /** @var Tag $tag */
        $tag2 = factory(Tag::class)->create([
            'type' => 'automatic',
            'name' => 'credits/income',
            'user_id' => $transaction->account->owner->id
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_LESS_THAN,
            'value' => '0'
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'fee'
        ]);
        $tag2->conditionals()->create([
            'parameter' => 'name',
            'comparator' => Condition::COMPARATOR_NOT_LIKE,
            'value' => 'transfer'
        ]);

        $handler = new ApplyGroupToTransactionAutomaticallyListener();
        $event = new TransactionCreated($transaction);
        $handler->handle($event);

        $tags = $transaction->tags()->get();
        $this->assertCount(1, $tags);
        $this->assertSame($tag->id, $tags->first()->id);
    }
}
