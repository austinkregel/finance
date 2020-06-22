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
            'type' => 'automatic',
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
                    'value' => 'disney'
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
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'patreon'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Subscription'
                ],
            ],
        ],

        [
            'name' => 'bills',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Utilities'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Insurance'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Billpay'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Loans and Mortgage'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Cable'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Telecommunication Services'
                ]
            ],
        ],
        [
            'name' => 'fast food',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Wendy\'s'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Arby\'s'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Taco Bell'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'McDonald\'s'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Burger King'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Subway'
                ],
                [
                    'parameter' => 'transaction.category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => "Fast Food"
                ]
            ],
        ],
        [
            'name' => 'fees',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'fee'
                ],
            ],
        ],
        [
            'name' => 'via Privacy.com',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_STARTS_WITH,
                    'value' => 'PWP*'
                ],
            ],
        ],
        [
            'name' => 'transfer',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'transfer'
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
            $conditions = $tagInfo['conditions'];
            unset($tagInfo['conditions']);

            /** @var Tag $tag */
            $tag = $user->tags()->create($tagInfo);
            foreach ($conditions as $condition) {
                $tag->conditionals()->create($condition);
            }
        }
    }
}
