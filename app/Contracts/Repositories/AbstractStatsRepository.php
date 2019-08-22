<?php
/**
 * AbstractStatsRepository
 */
declare(strict_types=1);

namespace App\Contracts\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class AbstractStatsRepository
 * @package App\Contracts\Repositories
 */
interface AbstractStatsRepository
{
    /**
     * @param array $accountIds
     * @return Collection
     */
    public function findAllForTheLastWeekByAccountIdsAndDate(array $accountIds): Collection;

    /**
     * @param array $accountIds
     * @return Collection
     */
    public function findAllForTheLastMonthByAccountIdsAndDate(array $accountIds): Collection;

    /**
     * @param array $accountIds
     * @param Carbon $date
     * @return Collection
     */
    public function findAllByAccountIdsForDate(array $accountIds, Carbon $date): Collection;

}
