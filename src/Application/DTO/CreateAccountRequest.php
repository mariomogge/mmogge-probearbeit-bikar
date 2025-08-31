<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateAccountRequest
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public ?int $initialDepositCents = null
    ) {}
}
