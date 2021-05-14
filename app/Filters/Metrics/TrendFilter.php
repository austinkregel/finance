<?php

declare(strict_types=1);

namespace App\Filters\Metrics;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class TrendFilter
{
    public function handle(array $duration, Collection $data): array
    {
        [$durationStart, $durationEnd] = $duration;

        $numericalDuration = $durationStart->diffInDays($durationEnd);

        if ($numericalDuration > 31) {
            $numericalDuration = $durationStart->diffInMonths($durationEnd);
        }
        $returnedData = [];

        for ($i = 0; $i < $numericalDuration; $i++) {
            $returnedData[$durationStart->copy()->addDay($i)->format('m/d')] = 0;
        }

        $data->each(function (Transaction $transaction) use (&$returnedData): void {
            if (! array_key_exists($transaction->date->format('m/d'), $returnedData)) {
                $returnedData[$transaction->date->format('m/d')] = 0;
            }

            $returnedData[$transaction->date->format('m/d')] += $transaction->amount;
        });

        return $returnedData;
    }
}
