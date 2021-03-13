<?php

namespace App\Listeners;

use App\Budget;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Filters\TransactionsConditionFilter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class TriggerAlertForBreachedBudget implements ShouldQueue
{
    use InteractsWithQueue;

    protected TransactionsConditionFilter $filter;

    /**
     * @param BudgetBreachedEstablishedAmount $event
     */
    public function handle($event): void
    {
        $budget = $event->getBudget();

        $budget = Budget::with('user')->find($budget->id);

        $user = $budget->user;

        /** @var Collection $alertsToTrigger */
        $alertsToTrigger = $user->alerts()
            ->whereJsonContains('events', BudgetBreachedEstablishedAmount::class)
            ->get();

        $transaction = $event->getTransaction();

        if ($transaction !== null) {
            $alertsToTrigger->map->createBudgetBreachNotificationWithTransaction($transaction,$budget);
            return;
        }

        $alertsToTrigger->map->createBudgetBreachNotification($budget);
    }
}
