<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/accounts')]
class AccountController extends AbstractController
{
    private const STANDARD_CURRENCY = 'EUR';

    public function __construct(private EntityManagerInterface $em) {}

    private function assertOwner(Account $account, User $user): void
    {
        if ($account->getOwner()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('This is not your account.');
        }
    }

    #[Route('', name: 'create_account', methods: ['POST'])]
    public function create(#[CurrentUser] User $user): JsonResponse
    {
        $account = (new Account())->setOwner($user)->setBalanceEuros(0);
        $this->em->persist($account);
        $this->em->flush();

        return $this->json([
            'id' => $account->getId(),
            'balance' => $account->getBalanceEuros(),
            'currency' => self::STANDARD_CURRENCY
        ], 201);
    }

    #[Route('/{id</d+>', name: 'get_account', methods: ['GET'])]
    public function getOne(int $id, #[CurrentUser] User $user): JsonResponse
    {
        $account = $this->em->find(Account::class, $id);

        if (!$account) {
            return $this->json([
                'error' => 'Account not found.'
            ]. 404);
        }

        $this->assertOwner($account, $user);

        return $this->json([
            'id' => $account->getId(),
            'balance' => $account->getBalanceEuros(),
            'currency' => self::STANDARD_CURRENCY
        ]);
    }

    #[Route('{id<\d+>}/transactions', name: 'get_transactions', methods: ['GET'])]
    public function transactions(int $id, #[CurrentUser] User $user): JsonResponse
    {
        $account = $this->em->find(Account::class, $id);

        if (!$account) {
            return $this->json(['error' => 'Account not found.']);
        }

        $this->assertOwner($account, $user);

        $transactions = $this->em->getRepository(Transaction::class)->findBy(
            ['account' => $account],
            ['createdAt' => 'DESC', 'id' =>'DESC'],
            100
        );

        return $this->json(array_map(fn(Transaction $trans) => [
            'id' => $trans->getId(),
            'type' => $trans->getType(),
            'amount' => $trans->getAmountEuros(),
            'balanceAfter' => $trans->getBalanceAfterEuros(),
            'createdAt' => $trans->getCreatedAt()->format(DATE_ATOM)
        ], $transactions));
    }

    #[Route('/{id<\d+>}/deposit', name: 'account_deposit', methods: ['POST'])]
    public function deposit(int $id, Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $account = $this->em->getRepository(Account::class)->find($id);

        if (!$account) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $this->assertOwner($account, $user);

        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];
        
        $account->deposit($amount);

        $transaction = (new Transaction())
            ->setAccount($account)
            ->setType('deposit')
            ->setAmountEuros((string)$amount)
            ->setBalanceAfterEuros($account->getBalanceEuros());

        $this->em->persist($transaction);
        $this->em->flush();

        return $this->json([
            'balance' => $account->getBalanceEuros(),
            'transactionId' => $transaction->getId()
        ], 201);
    }

    #[Route('/{id<\d+>}/withdraw', name: 'account_withdraw', methods: ['POST'])]
    public function withdraw(int $id, Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $account = $this->em->getRepository(Account::class)->find($id);

        if (!$account) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $this->assertOwner($account, $user);

        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];

        try {
            $account->withdraw($amount);
        } catch (\DomainException $e) {
            return $this->json(['error' => 'Insufficient funds'], 409);
        }

        $transaction = (new Transaction())
            ->setAccount($account)
            ->setType('withdraw')
            ->setAmountEuros((string)$amount)
            ->setBalanceAfterEuros($account->getBalanceEuros());

        $this->em->persist($transaction);
        $this->em->flush();

        return $this->json([
            'balance' => $account->getBalanceEuros(),
            'transactionId' => $transaction->getId()
        ], 201);
    }
}
