<?php

namespace App\Domain\Account;

use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Repository\AccountRepository::class)]
#[ORM\Table(name: 'accounts')]
class Account
{
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

    public function withdraw(Money $amount): void
    {
        if ($amount->cents > $this->balanceCents) {
            throw new \DomainException('Insufficient funds: overdrafts are not allowed.');
        }
        $this->balanceCents -= $amount->cents;
    }
}
