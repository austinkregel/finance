<?php
/**
 * AbstractRepositoryInterface
 */
declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AbstractRepositoryInterface
{
    /**
     * Find all items.
     * @param array $where
     * @param array $with
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAll(array $where = [], array $with = [], int $limit = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Find all items.
     * @param string $orderBy
     * @param array $where
     * @param array $with
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAllOrderByDesc(string $orderBy, array $where = [], array $with = [], int $limit = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Find one item.
     * @param array $where
     * @param array $with
     * @return null|Model
     */
    public function findOne(array $where = [], array $with = []): ?Model;

    /**
     * Find one item.
     * @param array $where
     * @return Model
     */
    public function findOneWithAll(array $where = []): Model;

    /**
     * Find all items without pagination.
     * @param array $where
     * @param array $with
     * @return Collection
     */
    public function findAllWithoutPagination(array $where = [], array $with = []): Collection;

    /**
     * Checks to see if anything exists given some conditionals.
     * @param array $where
     * @return bool
     */
    public function existsBy(array $where = []): bool;
}
