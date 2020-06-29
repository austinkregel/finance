<?php

namespace App\Contracts\Repositories;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Interface TransactionRepository.
 *
 * @package namespace App\Contracts\Repositories;
 */
interface TransactionRepositoryContract extends AbstractRepositoryInterface
{
    public function findAllBetweenDateForUserInScope(User $user, Carbon $startDate, Carbon $endDate, string $scope): Collection;
}
