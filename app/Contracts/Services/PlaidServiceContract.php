<?php
/**
 * PlaidService
 */
declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Support\Collection;

/**
 * Interface PlaidServiceContract
 * @package App\Contracts\Services
 */
interface PlaidServiceContract
{
    /**
     * Get the institutions provided by plaid
     * @param string $bankName
     * @return array
     */
    public function getInstitutionsByName(string $bankName): array;

    /**
     * Get the institutions provided by plaid
     * @param string $bankId
     * @return Collection
     */
    public function getInstitutionsById(string $bankId): Collection;

    /**
     * Get the transactions for an account
     * @param string $accessToken
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function getTransactions(string $accessToken, Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Exchange the public token for a new access token
     * @param string $publicToken
     * @return array
     */
    public function getAccessToken(string $publicToken): array;

    /**
     * Get the accounts for the token
     * @param string $accessToken
     * @return array
     */
    public function getAccounts(string $accessToken): array;

    /**
     * @param Account $account
     * @return Account
     */
    public function rotateAccessTokens(Account $account): Account;

    /**
     * @return array
     */
    public function getCategories(): array;

    /**
     * @param int $count
     * @param int $page
     * @return LengthAwarePaginatorContract
     */
    public function getInstitutions(int $count = 500, int $page = 1): LengthAwarePaginatorContract;
    public function createLinkToken(string $userId): array;
    public function updateLinkToken(string $userId, string $accessToken): array;
}
