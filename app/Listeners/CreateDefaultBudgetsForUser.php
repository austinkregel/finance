<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Budget;
use App\Tag;
use App\User;
use Illuminate\Auth\Events\Registered;

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
            'tags' => ['bills'],
        ],
        [
            'name' => 'Subscriptions',
            'amount' => '100',
            'frequency' => 'MONTHLY',
            'interval' => '1',
            'started_at' => '',
            'count' => null,
            'tags' => ['subscriptions'],
        ],
    ];

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        foreach (static::BUDGETS as $budget) {
            $budget['started_at'] = now()->startOfMonth();
            $tags = $budget['tags'];
            unset($budget['tags']);
            /** @var Budget $budget */
            $budget = $user->budgets()->create($budget);

            $tags = array_map(fn ($tagName) => Tag::findFromString($tagName, 'automatic')->id, $tags);

            $budget->tags()->syncWithoutDetaching($tags);
        }
    }
}
