<?php

namespace App\Http\Controllers\Api;

use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Filters\SubscriptionActionFilter;
use Illuminate\Validation\ValidationException;
use RRule\RRule;

class SubscriptionsController
{
    public function __invoke(Request $request)
    {
        $with = array_filter(explode(',', $request->get('include', '')));

        /** @var Collection|Subscription[] $subscriptions */
        $subscriptions = auth()->user()->subscriptions()
            ->with($with)
            ->get()->map(function (Subscription $subscription) {

            $endedAtString = $subscription['ended_at'] ?? null;
            $startedAtString = $subscription['started_at'];

            $endedAt = empty($endedAtString) ? null : $endedAtString instanceof Carbon ? $endedAtString : Carbon::parse($endedAtString);

            $frequency = $subscription['frequency'];
            $interval = $subscription['interval'];
            $startedAt = $startedAtString instanceof Carbon ? $startedAtString : Carbon::parse($startedAtString);

            $rrule = [
                'FREQ' => $frequency,
                'INTERVAL' => $interval,
                'DTSTART' => $startedAt,
            ];

            if (!empty($endedAtString)) {
                if ($endedAt->lessThanOrEqualTo(now()) && $endedAt->diffInMonths(now()) >= 1) {
                    return null;
                }

                $rrule['UNTIL'] = $endedAt;
            }

            $rule = new RRule($rrule);


            $subscription = $subscription->toArray();

            $subscription['due_currently'] = Arr::first($rule->getOccurrencesAfter(Carbon::now()->startOfMonth(), true, 1));
            $subscription['due_next'] = Arr::first($rule->getOccurrencesAfter(Carbon::now()->addMonth()->startOfMonth(), true, 1));

            return $subscription;
        })->filter(function ($subscription) {
            return Carbon::instance($subscription['due_currently'])->isAfter(now()->startOfMonth())
                && Carbon::instance($subscription['due_currently'])->isBefore(now()->endOfMonth());
        });

        $filters = request()->get('filters', []);

        $results = $subscriptions;
        $filter = new SubscriptionActionFilter(request()->get('action', ''));

        foreach ($filters as $query) {
            $results = $results->where(...$query);
        }

        return $filter->execute($results->sortBy('current_due_date')->values());
    }
}
