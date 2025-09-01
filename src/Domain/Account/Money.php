<?php

namespace App\Domain\Account;

// Value object for amounts of money.
// Amounts are saved in EUR cents to avoid rounding errors.
final readonly class Money
{
    public function __construct(public int $cents)
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException('Amount must be non-negative.');
        }
    }

    // factory method: create money from EUR (flot -> int)
    public static function fromEuros(float $euros): self
    {
        return new self((int) round($euros * 100));
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    // substraction with lower limit -> avoids negative values
    public function sub(self $other): self
    {
        return new self(max(0, $this->cents - $other->cents));
    }

    // return amount in EUR
    public function toEuros(): float
    {
        return $this->cents / 100;
    }
}
