<?php

namespace App\Domain\Account;


enum TransactionType: string
{
    case DEPOSIT = 'DEPOSIT';
    case WITHDRAW = 'WITHDRAW';
}
