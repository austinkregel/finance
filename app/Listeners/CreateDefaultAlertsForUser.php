<?php

namespace App\Listeners;

use App\Condition;
use App\Events\TransactionCreated;
use App\Models\Alert;
use App\Tag;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Notifications\Channels\DatabaseChannel;

class CreateDefaultAlertsForUser
{
    protected const ALERTS = [
        [
            'name' => 'fees',
            'title' => 'You\'ve been charged a {{ transaction.name }} fee',
            'body' => 'The fee is in the amount of ${{ transaction.amount }} on {{ transaction.amount }}',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'fee'
                ],
            ],
            'events' => [
                TransactionCreated::class,
            ],
            'channels' => [
                DatabaseChannel::class,
            ]
        ]
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

        $user->update([
            'alert_channels' => array_merge($user->alert_channels ?? [], [
                DatabaseChannel::class,
            ])
        ]);

        foreach (static::ALERTS as $alertInfo) {
            $conditions = $alertInfo['conditions'];
            unset($alertInfo['conditions']);

            /** @var Alert $alert */
            $alert = $user->alerts()->create($alertInfo);
            foreach ($conditions as $condition) {
                $alert->conditionals()->create($condition);
            }
        }
    }
}
