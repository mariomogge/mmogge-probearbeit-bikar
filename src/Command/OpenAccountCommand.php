<?php

namespace App\Command;

use App\Application\Service\AccountService;
use App\Domain\Account\Money;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:account:open', 'Open a new account for a user (by email).')]
final class OpenAccountCommand extends Command
{
    public function __construct(private AccountService $service, private UserRepository $users)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('initial', InputArgument::OPTIONAL, 'Initial cents', '0');
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $user = $this->users->findOneBy(['email' => $in->getArgument('email')]);
        if (!$user) {
            $out->writeln('<error>User not found</error>');
            return 1;
        }
        $acc = $this->service->openAccount($user, new Money((int)$in->getArgument('initial')));
        $out->writeln('Account ID: ' . $acc->getId());
        return 0;
    }
}
