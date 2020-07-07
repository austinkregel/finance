<?php

namespace App\Listeners;

use App\Contracts\Events\TransactionEventContract;
use App\Events\TransactionGroupedEvent;
use App\Filters\TransactionsConditionFilter;
use App\Models\Alert;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class TriggerAlertIfConditionsPassListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected TransactionsConditionFilter $filter;

    /**
     * Handle the event.
     *
     * @param TransactionGroupedEvent|TransactionEventContract $event
     * @return void
     */
    public function handle($event)
    {
        $this->filter = new TransactionsConditionFilter;
        $user = $event->getTransaction()->account->owner;
        $user->load('alerts.conditionals');

        if ($event instanceof TransactionGroupedEvent) {
            $event->transaction->tag = $event->getTag();
        }

        if ($this->shouldNotNotifyAbout($event, $user)) {
            // Our event didn't pass our conditionals. So let's kill the task.
            return;
        }

        // Obvs we should send the alert, but does it need a tag?
        if ($event instanceof TransactionGroupedEvent) {
            return $this->handleGroupedEvent($event, $user);
        }

        return $this->handleTransactionEvent($event, $user);
    }

    protected function handleGroupedEvent(TransactionGroupedEvent $event, User $user)
    {
        $tag = $event->getTag();

        /** @var Collection $alerts */
        $alerts = $user->alerts;

        $transaction = $event->getTransaction();
        $transaction->load(['tags', 'category', 'account']);
        /** @var Collection|Alert $alertsToTrigger */
        $alertsToTrigger = $alerts->filter(function (Alert $alert) use ($transaction) {
            // Should be empty if the transaction fails the alert's conditionals.
            return !empty($this->filter->handle($alert, $transaction));
        });

        $alertsToTrigger->map->createNotificationWithTag($transaction, $tag);
    }

    protected function handleTransactionEvent(TransactionEventContract $event, User $user)
    {
        $transaction = $event->getTransaction();
        $transaction->load(['account.owner.alerts']);

        /** @var Collection $alerts */
        $alerts = $transaction->account->owner->alerts;

        $alertsToTrigger = $alerts->filter(function (Alert $alert) use ($transaction) {
            // Should be empty if the transaction fails the alert's conditionals.
            return !empty($this->filter->handle($alert, $transaction));
        });

        $alertsToTrigger->map->createNotification($transaction);
    }

    protected function shouldNotNotifyAbout(TransactionEventContract $event, User $user)
    {
        $alerts = $user->alerts;
        $transaction = $event->getTransaction();

        $transaction->load(['user', 'category']);

        $shouldNotAlertMe = true;
        /** @var Alert $alert */
        foreach ($alerts as $alert) {
            // If the current event' isn't chosen by the user to be notified about, let's ignore this shiz.
            if (!in_array(get_class($event), $alert->events)) {
                continue;
            }

            $transactions = $this->filter->handle($alert, $transaction);

            if (empty($transactions)) {
                // True here because we don't want to notify anyone if the transaction doesn't pass filters.
                continue;
            }

            $shouldNotAlertMe = false;
        }

        // False here basically means we're going to notify someone about something.
        return $shouldNotAlertMe;
    }
}
