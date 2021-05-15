<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\BudgetBreachedEstablishedAmount;
use App\Filters\TransactionsConditionFilter;
use App\Models\Alert;
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
        $budget = $event->getData()['budget'];
        $user = $budget->user;

        /** @var Collection|Alert[] $alertsToTrigger */
        $alertsToTrigger = $user->alerts()
            ->whereJsonContains('events', BudgetBreachedEstablishedAmount::class)
            ->get();

        $transaction = $event->getData()['transaction'];

        if ($transaction !== null) {
            $alertsToTrigger->map->createBudgetBreachNotificationWithTransaction($event->getData());

            return;
        }

        $alertsToTrigger->map->createBudgetBreachNotification($event->getData()['budget']);
    }
}
