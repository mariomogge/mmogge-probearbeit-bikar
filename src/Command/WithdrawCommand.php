<?php

namespace App\Command;

use App\Application\Service\AccountService;
use App\Domain\Account\Money;
use App\Infrastructure\Repository\AccountRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:account:withdraw', 'Withdraw cents from an account.')]
final class WithdrawCommand extends Command
{
    public function __construct(private AccountService $service, private AccountRepository $accounts)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('accountId', InputArgument::REQUIRED)
            ->addArgument('cents', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $acc = $this->accounts->find($in->getArgument('accountId'));
        if (!$acc) {
            $out->writeln('<error>Account not found</error>');
            return 1;
        }
        $this->service->withdraw($acc, new Money((int)$in->getArgument('cents')));
        $out->writeln('OK');
        return 0;
    }
}
