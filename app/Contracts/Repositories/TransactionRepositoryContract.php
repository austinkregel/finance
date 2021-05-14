<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Interface TransactionRepository.
 */
interface TransactionRepositoryContract extends AbstractRepositoryInterface
{
    public function findAllBetweenDateForUserInScope(User $user, Carbon $startDate, Carbon $endDate, string $scope): Collection;
}
