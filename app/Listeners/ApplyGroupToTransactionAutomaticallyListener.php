<?php

namespace App\Listeners;

use App\Contracts\Events\TransactionEventContract;
use App\Events\TransactionGroupedEvent;
use App\Filters\TransactionsConditionFilter;
use App\Tag;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ApplyGroupToTransactionAutomaticallyListener implements ShouldQueue
{
    protected TransactionsConditionFilter $filter;

    public function __construct()
    {
        $this->filter = new TransactionsConditionFilter();
    }

    public function handle(TransactionEventContract $event)
    {
        /** @var Transaction $transaction */
        $transaction = $event->getTransaction();
        $transaction->load(['user', 'category']);

        $user = $transaction->user;
        /** @var Collection $groupsForUser */
        $groupsForUser = cache()->remember('automatic-conditions.'.$user->id, now()->addMinute(), function () use ($user) {
            return Tag::withType('automatic')->with('conditionals')->where('user_id', $user->id)->get();
        });
        $groupsForUser->each(function (Tag $group) use ($transaction) {
            if ($group->conditionals->count() === 0) {
                // Shortcut. save the group to the conditional.
                $transaction->attachTag($group);
                event(new TransactionGroupedEvent($group, $transaction));
                return;
            }

            /** @var Transaction $transaction */
            $transaction = Arr::first($this->filter->handle($group, $transaction));

            if (empty($transaction)) {
                return;
            }

            if ($transaction->tags()->where('tag_id', $group->id)->exists()) {
                // Don't double attach tags...
                return ;
            }

            $transaction->attachTag($group);
            event(new TransactionGroupedEvent($group, $transaction));
        });
    }
}
