<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private User $owner;

    private int $balanceEuros = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBalanceEuros(): int
    {
        return $this->balanceEuros;
    }

    public function setBalanceEuros(int $amount): self
    {
        $this->balanceEuros = max(0, $amount);

        return $this;
    }

    public function deposit(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Deposit must be >= 0.');
        }

        $this->balanceEuros += $amount;
    }

    public function withdraw(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Withdraw must be >= 0.');
        }

        if ($this->balanceEuros < $amount) {
            throw new \DomainException('Insufficient funds.');
        }

        $this->balanceEuros -= $amount;
    }
}
