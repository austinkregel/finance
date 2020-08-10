<?php

namespace App\Listeners;

use App\Budget;
use App\Contracts\Events\TransactionEventContract;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Events\TransactionGroupedEvent;
use App\Filters\TransactionsConditionFilter;
use App\Models\Alert;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class TriggerAlertForBreachedBudget implements ShouldQueue
{
    use InteractsWithQueue;

    protected TransactionsConditionFilter $filter;

    public function handle($event)
    {
        $budget = $event->getBudget();

        $budget = Budget::totalSpends()->with('user')->find($budget->id);

        $user = $budget->user;

        /** @var Collection $alertsToTrigger */
        $alertsToTrigger = $user->alerts()
            ->whereJsonContains('events', BudgetBreachedEstablishedAmount::class)
            ->get();

        $alertsToTrigger->map->createBudgetBreachNotification($budget);
    }
}
