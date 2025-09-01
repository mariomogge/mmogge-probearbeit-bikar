<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

// Explicit validation keeps controller slim and reusable
final class WithdrawRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Positive]
        public int $amountCents
    ) {}
}
