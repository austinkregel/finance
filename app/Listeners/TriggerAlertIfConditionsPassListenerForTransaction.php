<?php

namespace App\Listeners;

use App\Contracts\Events\TransactionEventContract;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Events\TransactionGroupedEvent;
use App\Filters\TransactionsConditionFilter;
use App\Models\Alert;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TriggerAlertIfConditionsPassListenerForTransaction implements ShouldQueue
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
        if ($event->getShouldSendAlerts() === false) {
            // In other words, we should not send alerts.
            return;
        }

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
            $this->handleGroupedEvent($event, $user);
            return;
        }
        // So in the future, to add new event types this should probably get refactored...
        // but for now, doing this one final time.
        if ($event instanceof BudgetBreachedEstablishedAmount) {
            $this->handleBudgetEvent($event, $user);
            return;
        }

        $this->handleTransactionEvent($event, $user);
    }

    protected function handleGroupedEvent(TransactionGroupedEvent $event, User $user): void
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

    protected function handleTransactionEvent(TransactionEventContract $event, User $user): void
    {
        $transaction = $event->getTransaction();
        $transaction->load(['account.owner.alerts']);

        /** @var Collection $alerts */
        $alerts = $transaction->account->owner->alerts;

        $alertsToTrigger = $this->alertsToTrigger($alerts, $event, $transaction);

        $alertsToTrigger->map->createNotification($transaction);
    }

    protected function alertsToTrigger(Collection $alerts, $event, $transaction)
    {
        return $alerts->filter(function ($alert) use ($event, $transaction) {
            if (!in_array(get_class($event), $alert->events)) {
                return false;
            }

            $transactions = $this->filter->handle($alert, $transaction);

            if (empty($transactions)) {
                return false;
            }

            if ($this->alertHasAlreadyBeenTriggeredRecentlyForThisTransaction($alert, $transaction)) {
                return false;
            }

            return true;
        });
    }

    protected function shouldNotNotifyAbout(TransactionEventContract $event, User $user)
    {
        $alerts = $user->alerts;
        $transaction = $event->getTransaction();

        $transaction->load(['user', 'category']);

        return $this->alertsToTrigger($alerts, $event, $transaction)->count() === 0;
    }

    protected function handleBudgetEvent(BudgetBreachedEstablishedAmount $event, $user): void
    {
        $budget = $event->getBudget();

        /** @var Collection $alerts */
        $alerts = $user->alerts;

        $transaction = $event->getTransaction();
        $transaction->load(['tags', 'category', 'account']);
        /** @var Collection|Alert $alertsToTrigger */
        $alertsToTrigger = $this->alertsToTrigger($alerts, $event, $transaction);

        $alertsToTrigger->map->createNotificationWithBudget($transaction, $budget);
    }

    protected function alertHasAlreadyBeenTriggeredRecentlyForThisTransaction($alert, $transaction)
    {
        return DB::table('alert_logs')
            ->where('triggered_by_transaction_id', $transaction->id)
            ->where('alert_id', $alert->id)
            ->where('created_at', now()->startOfDay())
            ->exists();
    }
}
