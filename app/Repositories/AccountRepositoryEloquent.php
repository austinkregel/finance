<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AccountRepository;
use App\Models\Account;
use Illuminate\Support\Collection;

/**
 * Class AccountRepositoryEloquent.
 */
class AccountRepositoryEloquent extends AbstractRepository implements AccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Account::class;
    }

    /**
     * @return Collection
     */
    public function findAllByLoggedInUser(): Collection
    {
        return auth()->user()->accounts;
    }

    /**
     * @param int $id
     * @param array $with
     * @return Account
     */
    public function findOneWith(int $id, array $with = []): Account
    {
        return auth()->user()->accounts()->with(array_merge(['users'], $with))->find($id);
    }

    /**
     * @param string $id
     * @return Account|null
     */
    public function findOneById(string $id): ?Account
    {
        return auth()->user()->accounts()->where('accounts.account_id', $id)->first();
    }
}
