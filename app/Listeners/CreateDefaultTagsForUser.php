<?php
declare(strict_types=1);

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
                    'value' => 'hulu',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'disney',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'HBO',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'twitch',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'github',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'plex',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'protonmail',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'youtube',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Subscription',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Discord',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'netflix',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'WASABI TECHNOLOGIES',
                ],
            ],
        ],

        [
            'name' => 'games',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
            'conditions' => [
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'ORIGIN',
                ],
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Steampowered',
                ],
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Steam',
                ],
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'UBISOFT',
                ],
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'gamestop',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Video Games',
                ],
                [
                    'parameter' => 'transaction.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'game store',
                ],
            ],
        ],
        [
            'name' => 'bills',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
            'conditions' => [
                [
                    'parameter' => 'tag.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    // anything that's a utility should automatically be a bill
                    'value' => 'utilities',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Loans and Mortgages',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Billpay',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'USAA P&C INT AUTOPAY',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Car Dealers and Leasing',
                ],
            ],
        ],
        [
            'name' => 'utilities',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
            'conditions' => [
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Cable',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Telecommunication Services',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Utilities',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Sanitary and Waste Management',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    // This is for people who get their power/water from the city (like those in petoskey)
                    'value' => 'Government Departments and Agencies',
                ],
            ],
        ],
        [
            'name' => 'fast food/restaurants',
            'type' => 'automatic',
            'must_all_conditions_pass' => false,
            'conditions' => [
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'Restaurants',
                ],
                [
                    'parameter' => 'category.name',
                    'comparator' => Condition::COMPARATOR_EQUAL,
                    'value' => 'Fast Food',
                ],
            ],
        ],
        [
            'name' => 'fees',
            'type' => 'automatic',
            'conditions' => [
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_LIKE,
                    'value' => 'fee',
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
                    'value' => 'PWP*',
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
                    'value' => 'transfer',
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
                    'value' => 'transfer',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'fee',
                ],
                [
                    'parameter' => 'amount',
                    'comparator' => Condition::COMPARATOR_LESS_THAN,
                    'value' => 0,
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
                    'value' => 'transfer',
                ],
                [
                    'parameter' => 'name',
                    'comparator' => Condition::COMPARATOR_NOT_LIKE,
                    'value' => 'fee',
                ],
                [
                    'parameter' => 'amount',
                    'comparator' => Condition::COMPARATOR_GREATER_THAN,
                    'value' => 0,
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
    public function handle(Registered $event): void
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
