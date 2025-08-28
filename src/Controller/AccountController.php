<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/accounts')]
class AccountController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function create(EntityManagerInterface $em): JsonResponse
    {
        $account = new Account();
        $account->setOwner($this->getUser());
        $em->persist($account);
        $em->flush();

        return $this->json([
            'id' => $account->getId(),
            'balance' => $account->getBalanceEuros()
        ], 201);
    }

    #[Route('/{id}/deposit', methods: ['POST'])]
    public function deposit(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $account = $em->getRepository(Account::class)->find($id);
        if (!$account || $account->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $amount = (float)$data['amount'];
        $account->setBalanceEuros(bcadd($account->getBalanceEuros(), (string)$amount, 2));

        $transaction = (new Transaction())
            ->setAccount($account)
            ->setType('deposit')
            ->setAmountEuros((string)$amount);

        $em->persist($transaction);
        $em->flush();

        return $this->json(['balance' => $account->getBalanceEuros()], 201);
    }

    #[Route('/{id}/withdraw', methods: ['POST'])]
    public function withdraw(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $account = $em->getRepository(Account::class)->find($id);
        if (!$account || $account->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $amount = (float)$data['amount'];

        if ($amount > (float)$account->getBalanceEuros()) {
            return $this->json(['error' => 'Insufficient funds'], 409);
        }

        $account->setBalanceEuros(bcsub($account->getBalanceEuros(), (string)$amount, 2));

        $transaction = (new Transaction())
            ->setAccount($account)
            ->setType('withdraw')
            ->setAmountEuros((string)$amount);

        $em->persist($transaction);
        $em->flush();

        return $this->json(['balance' => $account->getBalanceEuros()], 201);
    }
}
