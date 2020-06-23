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

    /**
     * Handle the event.
     *
     * @param TransactionGroupedEvent|TransactionEventContract $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->getTransaction()->account->owner;
        $user->load('alerts.conditionals');

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
        $alertsToTrigger = $alerts->filter(function (Alert $alert) use ($transaction) {
            // Should be empty if the transaction fails the alert's conditionals.
            return !empty((new TransactionsConditionFilter)->handle($alert, $transaction));
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
            return !empty((new TransactionsConditionFilter)->handle($alert, $transaction));
        });

        $alertsToTrigger->map->createNotification($transaction);
    }

    protected function shouldNotNotifyAbout(TransactionEventContract $event, User $user)
    {
        $alerts = $user->alerts;
        $filter = new TransactionsConditionFilter();

        $transaction = $event->getTransaction();

        $transaction->load(['user', 'category']);

        /** @var Alert $alert */
        foreach ($alerts as $alert) {
            // If the current event' isn't chosen by the user to be notified about, let's ignore this shiz.
            if (!in_array(get_class($event), $alert->events)) {
                return true;
            }

            $transaction = $filter->handle($alert, $transaction);

            if (empty($transaction)) {
                // True here because we don't want to notify anyone if the transaction doesn't pass filters.
                return true;
            }
        }

        // False here basically means we're going to notify someone about something.
        return false;
    }
}
