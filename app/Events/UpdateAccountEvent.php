<?php

namespace App\Events;

use App\Models\Domain\Account;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UpdateAccountEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var User
     */
    protected $user;

    /**
     * UpdateAccountEvent constructor.
     * @param Account $account
     * @param User $user
     */
    public function __construct(Account $account, ?User $user = null)
    {
        $this->account = $account;
        $this->user = $user;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
