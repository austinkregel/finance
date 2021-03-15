<?php

namespace App\Contracts\Repositories;

use App\Models\Account;
use Illuminate\Support\Collection;

/**
 * Interface AccountRepository.
 */
interface AccountRepository extends AbstractRepositoryInterface
{
    /**
     * @return Account[]|Collection
     */
    public function findAllByLoggedInUser(): Collection;

    /**
     * @param int $id
     * @param array $with
     * @return Account
     */
    public function findOneWith(int $id, array $with = []): Account;

    /**
     * @param string $id
     * @return Account|null
     */
    public function findOneById(string $id): ?Account;
}
