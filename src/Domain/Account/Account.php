<?php

namespace App\Domain\Account;

use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

// Aggregate root: account belongs always one user only
// contains business logic for deposit and withdraw without overdrafting
#[ORM\Entity(repositoryClass: \App\Infrastructure\Repository\AccountRepository::class)]
#[ORM\Table(name: 'accounts')]
class Account
{
    private const INSUFFICIENT_FUNDS = 'Insufficient funds: overdrafts are not allowed.';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(type: 'integer')]
    private int $balanceCents = 0;


    public function __construct(User $owner)
    {
        // UUIDv7 for unique and sortable IDs
        $this->id = Uuid::v7()->toRfc4122();
        $this->owner = $owner;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getBalance(): Money
    {
        return new Money($this->balanceCents);
    }

    public function deposit(Money $amount): void
    {
        $this->balanceCents += $amount->cents;
    }

    /**
     * Overdraft not allowed -> throws DomainException
     * @param \App\Domain\Account\Money $amount
     * @throws \DomainException
     * @return void
     */
    public function withdraw(Money $amount): void
    {
        if ($amount->cents > $this->balanceCents) {
            throw new \DomainException(self::INSUFFICIENT_FUNDS);
        }
        $this->balanceCents -= $amount->cents;
    }
}
