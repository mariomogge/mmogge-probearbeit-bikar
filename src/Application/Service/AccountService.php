<?php

namespace App\Application\Service;

use App\Domain\Account\{Account, AccountServiceInterface, Money, Transaction, TransactionType};
use App\Domain\User\User;
use App\Infrastructure\Repository\{AccountRepository, TransactionRepository};
use Doctrine\ORM\EntityManagerInterface;

// Application service to orchestrate use cases(create account, deposit, withdraw).
// Does not contain business logic (see entities), only application logic.
final class AccountService implements AccountServiceInterface
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions,
        private readonly EntityManagerInterface $em,
    ) {}

    // Open account; optionally with an initial deposit
    public function openAccount(User $owner, ?Money $initialDeposit = null): Account
    {
        $account = new Account($owner);
        $this->em->persist($account);
        if ($initialDeposit && $initialDeposit->cents > 0) {
            $this->deposit($account, $initialDeposit);
        }
        $this->em->flush();
        return $account;
    }

    public function deposit(Account $account, Money $amount): Transaction
    {
        $account->deposit($amount);
        $tx = new Transaction($account, TransactionType::DEPOSIT, $amount, $account->getBalance());
        $this->em->persist($tx);
        $this->em->flush();
        return $tx;
    }

    public function withdraw(Account $account, Money $amount): Transaction
    {
        $account->withdraw($amount); // may throw DomainException. See method for details
        $tx = new Transaction($account, TransactionType::WITHDRAW, $amount, $account->getBalance());
        $this->em->persist($tx);
        $this->em->flush();
        return $tx;
    }
}
