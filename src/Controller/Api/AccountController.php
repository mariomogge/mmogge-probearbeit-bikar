<?php

namespace App\Controller\Api;

use App\Application\DTO\{CreateAccountRequest, DepositRequest, WithdrawRequest};
use App\Application\Security\AccountVoter;
use App\Application\Service\AccountService;
use App\Domain\Account\{Account, Money};
use App\Domain\User\User;
use App\Infrastructure\Repository\{AccountRepository, TransactionRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// REST controller for account operations.
// Uses DTOs for input validation and calls AccountService
#[Route('/api/accounts')]
final class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountService $service,
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions
    ) {}

    /**
     * Route for opening new account
     * 
     * @param Request $request
     * @param User $user // #[CurrentUser]
     * @param ValidatorInterface $validator
     * 
     * @return JsonResponse
     */
    #[Route('', methods: ['POST'])]
    public function open(Request $request, #[CurrentUser] User $user, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new CreateAccountRequest($data['initialDepositCents'] ?? null);
        $errors = $validator->validate($dto);
        if (count($errors)) {
            return $this->json(['errors' => (string) $errors], 400);
        }


        $initial = $dto->initialDepositCents ? new Money($dto->initialDepositCents) : null;
        $account = $this->service->openAccount($user, $initial);
        return $this->json(['id' => $account->getId(), 'balanceCents' => $account->getBalance()->cents], 201);
    }

    /**
     * Show account details
     * 
     * @param string $id
     * @param User $user // #[CurrentUser]
     * 
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id, #[CurrentUser] User $user): JsonResponse
    {
        $account = $this->accounts->find($id) ?? throw $this->createNotFoundException();
        $this->denyAccessUnlessGranted(AccountVoter::VIEW, $account);
        return $this->json([
            'id' => $account->getId(),
            'balanceCents' => $account->getBalance()->cents,
        ]);
    }

    /**
     * Show transactions by account user
     * 
     * @param string $id
     * @param User $user // #[CurrentUser]
     * @param Request $request
     * 
     * @return JsonResponse
     */
    #[Route('/{id}/transactions', methods: ['GET'])]
    public function transactions(string $id, #[CurrentUser] User $user, Request $request): JsonResponse
    {
        $account = $this->accounts->find($id) ?? throw $this->createNotFoundException();
        $this->denyAccessUnlessGranted(AccountVoter::VIEW, $account);
        $limit = (int)($request->query->get('limit', 50));
        $offset = (int)($request->query->get('offset', 0));
        $txs = $this->transactions->findForAccount($account, $limit, $offset);
        return $this->json(array_map(fn($t) => [
            'id' => $t->id ?? null, // keep DTO simple; or expose via getters in entity
        ], $txs));
    }

    /**
     * Deposit money amount by account user on user account
     * 
     * @param string $id
     * @param Request $request
     * @param User $user // #[CurrentUser]
     * @param ValidatorInterface $validator
     * 
     * @return JsonResponse
     */
    #[Route('/{id}/deposit', methods: ['POST'])]
    public function deposit(string $id, Request $request, #[CurrentUser] User $user, ValidatorInterface $validator): JsonResponse
    {
        $account = $this->accounts->find($id) ?? throw $this->createNotFoundException();
        $this->denyAccessUnlessGranted(AccountVoter::OPERATE, $account);


        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new DepositRequest($data['amountCents'] ?? null);
        $errors = $validator->validate($dto);
        if (count($errors)) {
            return $this->json(['errors' => (string) $errors], 400);
        }


        $tx = $this->service->deposit($account, new Money($dto->amountCents));
        return $this->json(['balanceCents' => $account->getBalance()->cents], 200);
    }

    /**
     * Withdraw money amount by account user on user account
     * 
     * @param string $id
     * @param Request $request
     * @param User $user // #[CurrentUser]
     * @param ValidatorInterface $validator
     * 
     * @return JsonResponse
     */
    #[Route('/{id}/withdraw', methods: ['POST'])]
    public function withdraw(string $id, Request $request, #[CurrentUser] User $user, ValidatorInterface $validator): JsonResponse
    {
        $account = $this->accounts->find($id) ?? throw $this->createNotFoundException();
        $this->denyAccessUnlessGranted(AccountVoter::OPERATE, $account);


        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new WithdrawRequest($data['amountCents'] ?? null);
        $errors = $validator->validate($dto);
        if (count($errors)) {
            return $this->json(['errors' => (string) $errors], 400);
        }


        $tx = $this->service->withdraw($account, new Money($dto->amountCents));
        return $this->json(['balanceCents' => $account->getBalance()->cents], 200);
    }
}
