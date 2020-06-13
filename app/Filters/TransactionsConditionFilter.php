<?php

namespace App\Filters;

use App\Condition;
use App\Contracts\Models\ConditionalsContract;
use App\Filters\Conditions\EndsWith;
use App\Filters\Conditions\EqualFilter;
use App\Filters\Conditions\GreaterThan;
use App\Filters\Conditions\GreaterThanEqual;
use App\Filters\Conditions\InFilter;
use App\Filters\Conditions\InLikeFilter;
use App\Filters\Conditions\LessThanEqualFilter;
use App\Filters\Conditions\LessThanFilter;
use App\Filters\Conditions\LikeFilter;
use App\Filters\Conditions\NotEqualFilter;
use App\Filters\Conditions\NotInFilter;
use App\Filters\Conditions\NotInLikeFilter;
use App\Filters\Conditions\NotLikeFilter;
use App\Filters\Conditions\StartsWith;
use App\Models\Traits\Conditionable;
use App\Models\Transaction;
use App\Tag;
use Spatie\Tags\Tag as SpatieTag;

class TransactionsConditionFilter
{
    public const CONDITIONS = [
        ConditionalsContract::COMPARATOR_ENDS_WITH => EndsWith::class,
        ConditionalsContract::COMPARATOR_EQUAL => EqualFilter::class,
        ConditionalsContract::COMPARATOR_GREATER_THAN => GreaterThan::class,
        ConditionalsContract::COMPARATOR_GREATER_THAN_EQUAL => GreaterThanEqual::class,
        ConditionalsContract::COMPARATOR_IN => InFilter::class,
        ConditionalsContract::COMPARATOR_IN_LIKE => InLikeFilter::class,
        ConditionalsContract::COMPARATOR_LESS_THAN_EQUAL => LessThanEqualFilter::class,
        ConditionalsContract::COMPARATOR_LESS_THAN => LessThanFilter::class,
        ConditionalsContract::COMPARATOR_LIKE => LikeFilter::class,
        ConditionalsContract::COMPARATOR_NOT_EQUAL => NotEqualFilter::class,
        ConditionalsContract::COMPARATOR_NOT_IN => NotInFilter::class,
        ConditionalsContract::COMPARATOR_NOT_IN_LIKE => NotInLikeFilter::class,
        ConditionalsContract::COMPARATOR_NOT_LIKE => NotLikeFilter::class,
        ConditionalsContract::COMPARATOR_STARTS_WITH => StartsWith::class,
    ];

    public function handle(Tag $conditionable, Transaction ...$transactions): array
    {
        /** @var Condition[] $conditions */
        $conditions = $conditionable->conditionals;

        // Dude we need to get this code tested.
        // We should be ensuring that all transactions get their filter applied correctly.
        return array_values(array_filter($transactions, function (Transaction $transaction) use ($conditions) {
            if (count($conditions) === 0) {
                // If there are no conditionals, apply the tag to everything.
                return true;
            }
            /** @var Condition $conditional */
            foreach ($conditions as $conditional) {
                $condition = static::CONDITIONS[$conditional->getComparator()];

                $condition = new $condition;

                if ($condition($transaction, $conditional)) {
                    // Only 1 of them needs to pass in order for the transaction to be "valid"
                    return true;
                }
            }

            return false;
        }));
    }
}
