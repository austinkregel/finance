<?php

namespace Tests\Integration\Listeners;

use App\Condition;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Listeners\TriggerAlertIfConditionsPassListener;
use App\Models\Alert;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class TriggerAlertIfConditionsPassListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleWithNoConditionalsCreatesAlert()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'is_templated' => true,
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);

        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('%s charged $%s', $transaction->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('Yo dog, maybe you shouldn\'t spend money on %s', $transaction->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleWithConditionalCreatesAlert()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 100,
        ]);

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'is_templated' => true,
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

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('%s charged $%s', $transaction->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('Yo dog, maybe you shouldn\'t spend money on %s', $transaction->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleWithConditionalDoesntCreatesAlert()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 10,
        ]);

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog, maybe you shouldn\'t spend money on {{transaction.name}}',
            'payload' => '{}',
            'is_templated' => true,
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

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty(\DB::table('notifications')->get()->all());
        // As long as notifications are empty, that means we're not creating alerts if it doesn't pass the conditionals!
    }

    public function testHandleNothingHappensWhenNoAlertExists()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'amount' => 100,
        ]);

        $event = new TransactionCreated($transaction);

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty(\DB::table('notifications')->get()->all());
    }

    public function testHandleCanUseTagInAlert()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'is_templated' => true,
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionGroupedEvent::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        $this->assertSame(sprintf('Hey! It looks like your tag %s just had a $%s charge!', $tag->name, $transaction->amount), json_decode(collect($notifications)->first()->data)->title);
        $this->assertSame(sprintf('You paid your %s %s', $transaction->name, $tag->name), json_decode(collect($notifications)->first()->data)->body);
    }

    public function testHandleDoesNothingWhenThereIsNoChannel()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'is_templated' => true,
            'channels' => [],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionGroupedEvent::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty($notifications = \DB::table('notifications')->get()->all());
    }

    public function testHandleDoesNothingWhenThereEventIsNotSelected()
    {
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        /** @var Alert $alert */
        $alert = $transaction->user->alerts()->create([
            'name' => 'Alert',
            'title' => 'Hey! It looks like your tag {{ tag.name.en }} just had a ${{transaction.amount}} charge!',
            'body' => 'You paid your {{transaction.name}} {{tag.name.en}}',
            'payload' => '{}',
            'is_templated' => true,
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [TransactionCreated::class],
        ]);

        $tag = factory(Tag::class)->create([
            'name' => ['en' => 'bill']
        ]);

        $event = new TransactionGroupedEvent($tag, $transaction);

        $listener = new TriggerAlertIfConditionsPassListener();

        $this->assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        $this->assertEmpty($notifications = \DB::table('notifications')->get()->all());
    }


}
