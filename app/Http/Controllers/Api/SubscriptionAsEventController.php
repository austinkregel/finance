<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Subscription;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class SubscriptionAsEventController extends Controller
{
    public function events()
    {
        return auth()->user()->subscriptions->map(function (Subscription $subscription) {
            /** @var Transaction $transaction */
            $transaction = $subscription->transactions->first();
            if (empty($transaction)) {
                return null;
            }

            $isPaid = !$transaction->pending && $transaction->date->format('Y-m') ===  now()->format('Y-md');
            $nextDueDateIsAfterToday = Carbon::parse($subscription->next_due_date)->isAfter(now());
            return [
                'title' => $subscription->name,
                'rrule' => [
                    'freq' => $subscription->frequency,
                    'interval' => $subscription->interval,
                    'dtstart' => $subscription->started_at,
                ],
                'backgroundColor' => $nextDueDateIsAfterToday && $isPaid ? '#f56565' : '#48bb78',
                'borderColor' => $nextDueDateIsAfterToday && $isPaid ? '#f56565' : '#48bb78'
            ];
        })->filter()->values();
    }

    public function index()
    {
        return auth()->user()->subscriptions->map(function (Subscription $subscription) {
            $subscription->load([
                'fiveTransactions.account',
                'account'
            ]);

            return $subscription;
        })->sortBy('next_sort')->values();
    }
}
