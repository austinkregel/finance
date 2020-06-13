<?php

namespace App\Listeners;

use App\Condition;
use App\Tag;
use App\User;
use Illuminate\Auth\Events\Registered;

class CreateDefaultTagsForUser
{
    protected const TAGS = [
        [
            'name' => 'subscriptions',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'netflix'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'hulu'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'disney+'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'HBO'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'mixer'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'twitch'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'github'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'plex'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'protonmail'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'youtube'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'spotify'
                ],
            ],

        ],
        [
            'name' => 'fast food',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'wendys'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'arbys'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'taco bell'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'mcdonalds'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'burger king'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'subway'
                ],
            ],
        ],

        [
            'name' => 'fees',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'fee'
                ],
            ],
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

        foreach (static::TAGS as $tagInfo) {
            ['name' => $name, 'conditions' => $conditions] = $tagInfo;
            /** @var Tag $tag */
            $tag = $user->tags()->create(['name' => $name]);
            foreach ($conditions as $condition) {
                $tag->conditionals()->create($condition);
            }
        }
    }
}
