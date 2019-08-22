<?php

namespace App\Listeners;

use App\Contracts\Repositories\AccountRepository;
use App\Events\UpdateAccountEvent;
use App\Models\Account;

class UpdateAccountsListener
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * UpdateAccountsListener constructor.
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UpdateAccountEvent  $event
     * @return void
     */
    public function handle(UpdateAccountEvent $event)
    {
        $account = $event->getAccount();

        $user = $event->getUser();

        /** @var Account $accountEloquent */
        $accountEloquent = $this->accountRepository->findOne([['account_id', $account->account_id]]);
        if (empty($accountEloquent)) {
            /** @var Account $account */
            $account = $this->accountRepository->create([
                'account_id' => $account->account_id,
                'access_token_id' => $account->access_token_id,
                'mask' => $account->mask,
                'name' => $account->name,
                'official_name' => $account->official_name,
                'balance' => $account->balances->current ?? 0,
                'available' => $account->balances->available ?? 0,
                'subtype' => $account->subtype,
                'type' => $account->type
            ]);

            $account->users()->attach($user);
            return;
        }

        $accountEloquent->update([
            'mask' => $account->mask,
            'name' => $account->name,
            'official_name' => $account->official_name,
            'balance' => $account->balances->current ?? 0,
            'available' => $account->balances->available ?? 0,
            'subtype' => $account->subtype,
            'type' => $account->type
        ]);
    }
}
