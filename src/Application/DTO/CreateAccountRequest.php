<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

// Minimal: only data that this use case requires
final class CreateAccountRequest
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public ?int $initialDepositCents = null
    ) {}
}
