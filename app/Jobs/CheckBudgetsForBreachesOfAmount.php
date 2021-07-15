<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Budget;
use App\Events\BudgetBreachedEstablishedAmount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckBudgetsForBreachesOfAmount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle($page = 1): void
    {
        $events = [];
        do {
            /** @var Paginator $budgets */
            $budgets = Budget::query()->paginate(50, ['*'], '', $page++);

            /** @var Budget $budget */
            foreach ($budgets->items() as $budget) {
                // The last time the budget started it's current period.
                $startOfTheLastBudgetPeriod = $budget->getStartOfCurrentPeriod();
                // 80 minutes should give the system time to catch in-consistent runs.
                // The cron should run every hour, so things will only trigger once.
                if (! empty($budget->breached_at) && $startOfTheLastBudgetPeriod->diffInMinutes($budget->breached_at) > 80) {
                    // If the budget has already breached, and it did so several hours ago we don't want to spam users...
                    return;
                }

                if ($budget->amount < $budget->findTotalSpends($startOfTheLastBudgetPeriod) && empty($budget->breached_at)) {
                    event(new BudgetBreachedEstablishedAmount($budget));
                    $budget->update([
                        'breached_at' => now(),
                    ]);
                }
            }
        } while ($budgets->hasMorePages());
    }
}
