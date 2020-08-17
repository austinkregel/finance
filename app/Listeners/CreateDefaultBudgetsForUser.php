<?php
namespace App\Listeners;

use App\Budget;
use App\Condition;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Models\Alert;
use App\Tag;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel;

class CreateDefaultBudgetsForUser
{
    protected const BUDGETS = [
        [
            'name' => 'Bills',
            'amount' => '100',
            'frequency' => 'MONTHLY',
            'interval' => '1',
            'started_at' => '',
            'count' => null,
            'tags' => ['bills']
        ],
        [
            'name' => 'Subscriptions',
            'amount' => '100',
            'frequency' => 'MONTHLY',
            'interval' => '1',
            'started_at' => '',
            'count' => null,
            'tags' => ['subscriptions']
        ],
    ];

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        /** @var User $user */
        $user = $event->user;

        foreach (static::BUDGETS as $budget) {
            $budget['started_at'] = now();
            $tags = $budget['tags'];
            unset($budget['tags']);
            /** @var Budget $budget */
            $budget = $user->budgets()->create($budget);
            foreach ($tags as $tag) {
                $budget->attachTag($tag);
            }
        }
    }
}
