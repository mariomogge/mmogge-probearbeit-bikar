<?php

namespace App\Domain\Account;


final readonly class Money
{
    public function __construct(public int $cents)
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException('Amount must be non-negative.');
        }
    }


    public static function fromEuros(float $euros): self
    {
        return new self((int) round($euros * 100));
    }


    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }
    public function sub(self $other): self
    {
        return new self(max(0, $this->cents - $other->cents));
    }
    public function toEuros(): float
    {
        return $this->cents / 100;
    }
}
