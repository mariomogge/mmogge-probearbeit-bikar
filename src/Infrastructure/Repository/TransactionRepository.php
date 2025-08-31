<?php

namespace App\Infrastructure\Repository;

use App\Domain\Account\{Account, Transaction};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }


    /** @return Transaction[] */
    public function findForAccount(Account $account, int $limit = 100, int $offset = 0): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.account = :acc')->setParameter('acc', $account)
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}
