<?php

namespace Tests\Integration\Listeners;

use App\Condition;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Listeners\TriggerAlertIfConditionsPassListenerForTransaction;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Tests\TestCase;

class TriggerAlertIfConditionsPassListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleWithNoConditionalsCreatesAlert(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        $transaction->load('user.alerts');
        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);

        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('%s charged $%s', $transaction->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('Yo dog, maybe you shouldn\'t spend money on %s', $transaction->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleWithConditionalCreatesAlert(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 100,
        ]);
        $transaction->load('user.alerts');

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);
        $alert->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_GREATER_THAN,
            'value' => 50
        ]);
        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('%s charged $%s', $transaction->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('Yo dog, maybe you shouldn\'t spend money on %s', $transaction->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleWithConditionalDoesntCreatesAlert(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 10,
        ]);
        $transaction->load('user.alerts');

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);
        $alert->conditionals()->create([
            'parameter' => 'amount',
            'comparator' => Condition::COMPARATOR_GREATER_THAN,
            'value' => 50
        ]);
        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty(\DB::table('notifications')->get()->all());
        // As long as notifications are empty, that means we're not creating alerts if it doesn't pass the conditionals!
    }

    public function testHandleNothingHappensWhenNoAlertExists(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 100,
        ]);
        $transaction->load('user.alerts');

        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty(\DB::table('notifications')->get()->all());
    }

    public function testHandleCanUseTagInAlert(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        $transaction->load('user.alerts');

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionGroupedEvent::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('Hey! It looks like your tag %s just had a $%s charge!', $tag->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('You paid your %s %s', $transaction->name, $tag->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleDoesNothingWhenThereIsNoChannel(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        $transaction->load('user.alerts');

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'channels' => [],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionGroupedEvent::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty($notifications = \DB::table('notifications')->get()->all());
    }

    public function testHandleDoesNothingWhenThereEventIsNotSelected(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();
        $transaction->load('user.alerts');

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty($notifications = \DB::table('notifications')->get()->all());
    }

    public function testHandleCreateAlertBasedOnTag(): void
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'category_id' => Category::firstWhere('name', 'Loans and Mortgages')->category_id,
        ]);

        $transaction->load('user.alerts');
        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Bill paid!',
            'title' => 'You just paid your {{ transaction.name }} {{ tag.name.en }}!',
            'body' => 'This time around, you paid ${{ transaction.amount }}.',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionGroupedEvent::class],
        ]);

        $alert->conditionals()->create([
            'parameter' => 'tag.name.en',
            'comparator' => Condition::COMPARATOR_LIKE,
            'value' => 'bill'
        ]);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bills']
        ]);

        $tag->conditionals()->create([
            'parameter' => 'category.name',
            'comparator' => Condition::COMPARATOR_EQUAL,
            'value' => 'Loans and Mortgages',
        ]);

        $transaction->setRelations([]);
        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListenerForTransaction();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);

        $this->assertDatabaseHas('alert_logs', [
            'triggered_by_tag_id' => $tag->id,
            'triggered_by_transaction_id' => $transaction->id,
            'alert_id' => $alert->id,
        ]);

        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('You just paid your %s bills!', $transaction->name), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('This time around, you paid $%s.', $transaction->amount), json_decode(collect($notifications)->first()->data)->body);
    }
}
