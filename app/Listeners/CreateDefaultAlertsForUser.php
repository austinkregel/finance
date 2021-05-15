<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Condition;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Models\Alert;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel;

class CreateDefaultAlertsForUser
{
    protected const ALERTS = [
        [
            'name' => 'Charged a fee',
            'title' => 'You\'ve been charged a {{ transaction.name }} fee',
            'body' => 'The fee is in the amount of ${{ transaction.amount }} on {{ transaction.account.name }}',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'fee',
                ],
            ],
            'events' => [
                TransactionCreated::class,
            ],
            'channels' => [
                DatabaseChannel::class,
                MailChannel::class,
            ],
        ],
        [
            'name' => 'Bill paid!',
            'title' => 'You just paid your {{ transaction.name }} {{ tag.name.en }}!',
            'body' => 'This time around, you paid ${{ transaction.amount }}.',
            'conditions' => [
                [
                    'parameter' => 'tag.name.en',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'bill',
                ],
            ],
            'events' => [
                TransactionGroupedEvent::class,
            ],
            'channels' => [
                DatabaseChannel::class,
                MailChannel::class,
            ],
        ],
        [
            'name' => 'Subscription paid!',
            'title' => 'You just paid your {{ transaction.name }} {{ tag.name.en }}!',
            'body' => 'This time around, you paid ${{ transaction.amount }}.',
            'conditions' => [
                [
                    'parameter' => 'tag.name.en',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'subscription',
                ],
            ],
            'events' => [
                TransactionGroupedEvent::class,
            ],
            'channels' => [
                DatabaseChannel::class,
                MailChannel::class,
            ],
        ],
        [
            'name' => 'Budget spends exceeded amount!',
            'title' => 'Your {{budget.name}} just exceeded your set amount ${{budget.amount}}!',
            'body' => 'In total you have spent ${{budget.total_spends}}.',
            'conditions' => [],
            'events' => [
                BudgetBreachedEstablishedAmount::class,
            ],
            'channels' => [
                DatabaseChannel::class,
                MailChannel::class,
            ],
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

        $user->update([
            'alert_channels' => array_merge($user->alert_channels ?? [], [
                DatabaseChannel::class,
                MailChannel::class,
            ]),
        ]);

        foreach (static::ALERTS as $alertInfo) {
            $conditions = $alertInfo['conditions'];
            unset($alertInfo['conditions']);

            if (isset($alertInfo['channels'][MailChannel::class]) && isset($alertInfo['channels'][MailChannel::class]['fields']['to'])) {
                $alertInfo['channels'][MailChannel::class]['fields']['to'] = $user->email;
            }

            /** @var Alert $alert */
            $alert = $user->alerts()->create($alertInfo);
            foreach ($conditions as $condition) {
                $alert->conditionals()->create($condition);
            }
        }
    }
}
