<?php

namespace App\Repositories;

use App\Contracts\Repositories\TransactionRepositoryContract;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryContract
{
    /**
     * {@inheritdoc}
     */
    public function model(): string
    {
        return Transaction::class;
    }

    public function findAllBetweenDateForUserInScope(User $user, Carbon $startDate, Carbon $endDate, string $scope): Collection
    {
        $tag = $user
            ->tags()
            ->with([
                'transactions' => function (MorphToMany $query) use ($startDate, $endDate): void {
                    $query
                        ->selectRaw('id, sum(amount) as amount, date')
                        ->where('date', '>=', $startDate)
                        ->where('date', '<=', $endDate)
                        ->groupBy('date');
                },
            ])
            ->where('id', $scope)
            ->first();

        if (empty($tag)) {
            return Collection::make([]);
        }

        return $tag->transactions;
    }
}
