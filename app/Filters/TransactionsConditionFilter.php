<?php
declare(strict_types=1);

namespace App\Filters;

use App\Condition;
use App\Contracts\ConditionableContract;
use App\Contracts\ConditionalsContract;
use App\Filters\Conditions\EndsWith;
use App\Filters\Conditions\EqualFilter;
use App\Filters\Conditions\GreaterThan;
use App\Filters\Conditions\GreaterThanEqual;
use App\Filters\Conditions\InFilter;
use App\Filters\Conditions\LessThanEqualFilter;
use App\Filters\Conditions\LessThanFilter;
use App\Filters\Conditions\LikeFilter;
use App\Filters\Conditions\NotEqualFilter;
use App\Filters\Conditions\NotInFilter;
use App\Filters\Conditions\NotLikeFilter;
use App\Filters\Conditions\StartsWith;
use App\Models\Transaction;

class TransactionsConditionFilter
{
    public const CONDITIONS = [
        ConditionalsContract::COMPARATOR_ENDS_WITH => EndsWith::class,
        ConditionalsContract::COMPARATOR_EQUAL => EqualFilter::class,
        ConditionalsContract::COMPARATOR_GREATER_THAN => GreaterThan::class,
        ConditionalsContract::COMPARATOR_GREATER_THAN_EQUAL => GreaterThanEqual::class,
        ConditionalsContract::COMPARATOR_IN => InFilter::class,
        ConditionalsContract::COMPARATOR_LESS_THAN_EQUAL => LessThanEqualFilter::class,
        ConditionalsContract::COMPARATOR_LESS_THAN => LessThanFilter::class,
        ConditionalsContract::COMPARATOR_LIKE => LikeFilter::class,
        ConditionalsContract::COMPARATOR_NOT_EQUAL => NotEqualFilter::class,
        ConditionalsContract::COMPARATOR_NOT_IN => NotInFilter::class,
        ConditionalsContract::COMPARATOR_NOT_LIKE => NotLikeFilter::class,
        ConditionalsContract::COMPARATOR_STARTS_WITH => StartsWith::class,
    ];

    public function handle(ConditionableContract $conditionable, Transaction ...$transactions): array
    {
        $conditionable->load('conditionals');

        /** @var Condition[] $conditions */
        $conditions = $conditionable->conditionals;
        // Dude we need to get this code tested.
        // We should be ensuring that all transactions get their filter applied correctly.
        return array_values(array_filter($transactions, function (Transaction $transaction) use ($conditions, $conditionable) {
            if (count($conditions) === 0) {
                // If there are no conditionals, apply the tag to everything.
                return true;
            }

            $returnValue = true;
            /** @var Condition $conditional */
            foreach ($conditions as $conditional) {
                $condition = static::CONDITIONS[$conditional->getComparator()];

                $condition = new $condition();

                $passesCondition = $condition($transaction, $conditional);

                // Default behavior is to have all the conditions pass to do anything.
                // Here we want the `must_all_conditions_pass` variable to be false and have the condition pass
                // in order to get through this filter.
                if ($passesCondition && ! $conditionable->must_all_conditions_pass) {
                    return true;
                }

                if (! $passesCondition) {
                    $returnValue = false;
                }
            }

            // if any conditional fails, then we'll need to return false for this array filter so it's filtered out.
            return $returnValue;
        }));
    }
}
