<?php

namespace App\Domain\Account;

// Enum for transaction types
// Expandable for further types, e.g taxes, fees etc.
enum TransactionType: string
{
    case DEPOSIT = 'DEPOSIT';
    case WITHDRAW = 'WITHDRAW';
}
