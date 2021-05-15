<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\TransactionEventContract;
use App\Events\TransactionGroupedEvent;
use App\Filters\TransactionsConditionFilter;
use App\Models\Alert;
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
    public function handle($event): void
    {
        if ($event->getShouldSendAlerts() === false) {
            // In other words, we should not send alerts.
            return;
        }

        $transaction = $event->getData()['transaction'];
        $user = $transaction->account->owner;
        $user->load('alerts.conditionals');
        $alerts = $user->alerts;
        $this->filter = new TransactionsConditionFilter();

        $alertsToTrigger = $this->alertsToTrigger($alerts, $event, $transaction);

        $alertsToTrigger->map->triggerAlert($transaction, $event->getData());
    }

    protected function alertsToTrigger(Collection $alerts, $event, $transaction)
    {
        return $alerts->filter(function (Alert $alert) use ($event, $transaction) {
            if (! in_array(get_class($event), $alert->events)) {
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

    protected function alertHasAlreadyBeenTriggeredRecentlyForThisTransaction($alert, $transaction)
    {
        return DB::table('alert_logs')
            ->where('triggered_by_transaction_id', $transaction->id)
            ->where('alert_id', $alert->id)
            ->exists();
    }
}
