<?php

namespace App\Infrastructure\Repository;

use App\Domain\Account\Account;
use App\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }


    /** @return Account[] */
    public function findByOwner(User $owner): array
    {
        return $this->findBy(['owner' => $owner]);
    }
}
