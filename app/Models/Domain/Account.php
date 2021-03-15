<?php
/**
 * Account
 */
declare(strict_types=1);

namespace App\Models\Domain;

/**
 * Class Account
 * @property $account_id
 * @property $balances
 * @property $mask
 * @property $name
 * @property $official_name
 * @property $subtype
 * @property $type
 */
class Account
{
    public function __construct($account)
    {
        if (is_object($account)) {
            $iterable = get_object_vars($account);
        } else {
            $iterable = $account;
        }

        foreach ($iterable as $key => $value) {
            $this->$key = $value;
        }
    }
}
