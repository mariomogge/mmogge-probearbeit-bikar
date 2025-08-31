<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class DepositRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Positive]
        public int $amountCents
    ) {}
}
