<?php

namespace App\Domain\Account;

use App\Domain\User\User;

interface AccountServiceInterface
{
    public function openAccount(User $owner, ?Money $initialDeposit = null): Account;
    public function deposit(Account $account, Money $amount): Transaction;
    public function withdraw(Account $account, Money $amount): Transaction;
}
