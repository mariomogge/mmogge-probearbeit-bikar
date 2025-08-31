<?php

namespace App\Tests\Unit;

use App\Application\Service\AccountService;
use App\Domain\Account\{Account, Money};
use App\Domain\User\User;
use App\Infrastructure\Repository\{AccountRepository, TransactionRepository};
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class AccountServiceTest extends TestCase
{
    public function testDepositAndWithdraw(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');
        $em->method('flush');
        $svc = new AccountService(
            $this->createMock(AccountRepository::class),
            $this->createMock(TransactionRepository::class),
            $em
        );
        $user = new User('john@example.com');
        $acc = new Account($user);


        $svc->deposit($acc, new Money(10_00));
        $this->assertSame(10_00, $acc->getBalance()->cents);


        $svc->withdraw($acc, new Money(3_00));
        $this->assertSame(7_00, $acc->getBalance()->cents);
    }

    public function testOverdraftNotAllowed(): void
    {
        $this->expectException(\DomainException::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');
        $em->method('flush');
        $svc = new AccountService(
            $this->createMock(AccountRepository::class),
            $this->createMock(TransactionRepository::class),
            $em
        );
        $user = new User('john@example.com');
        $acc = new Account($user);
        $svc->withdraw($acc, new Money(1));
    }
}
