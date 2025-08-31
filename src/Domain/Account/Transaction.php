<?php
namespace App\Domain\Account;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Repository\TransactionRepository::class)]
#[ORM\Table(name: 'transactions')]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(enumType: TransactionType::class)]
    private TransactionType $type;

    #[ORM\Column(type: 'integer')]
    private int $amountCents;

    #[ORM\Column(type: 'integer')]
    private int $balanceAfterCents;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Account $account, TransactionType $type, Money $amount, Money $balanceAfter)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->account = $account;
        $this->type = $type;
        $this->amountCents = $amount->cents;
        $this->balanceAfterCents = $balanceAfter->cents;
        $this->createdAt = new \DateTimeImmutable();
    }
}
