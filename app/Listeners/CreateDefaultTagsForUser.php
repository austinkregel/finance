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
            'must_all_conditions_pass' => false,
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
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Subscription'
                ],
            ],
        ],
        [
            'name' => 'bills',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
            'conditions' => [
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Utilities'
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Insurance'
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Billpay'
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Loans and Mortgages'
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Cable'
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Telecommunication Services'
                ]
            ],
        ],
        [
            'name' => 'fast food',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
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
                    'parameter' => 'category.name',
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
        ],
        [
            'name' => 'credit/income',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'transfer'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'fee'
                ],
                [
                    'parameter' => 'amount',
                    'comparator' => Condition::COMPARATOR_LESS_THAN,
                    'value' => 0
                ],
            ],
        ],
        [
            'name' => 'debit/expense',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'transfer'
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'fee'
                ],
                [
                    'parameter' => 'amount',
                    'comparator' => Condition::COMPARATOR_GREATER_THAN,
                    'value' => 0
                ],
            ],
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
