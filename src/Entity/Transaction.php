<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Account $account;

    #[ORM\Column(length: 16)]
    private string $type;

    #[ORM\Column(type: 'integer', options: ['unsigned => true'])]
    private int $amountEuros;

    #[ORM\Column(type: 'integer', options: ['unsigned => true'])]
    private int $balanceAfterEuros;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmountEuros(): int
    {
        return $this->amountEuros;
    }

    public function setAmountEuros(int $amount): self
    {
        $this->amountEuros = $amount;

        return $this;
    }

    public function getBalanceAfterEuros(): int
    {
        return $this->balanceAfterEuros;
    }

    public function setBalanceAfterEuros(int $amount): self
    {
        $this->balanceAfterEuros = $amount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
