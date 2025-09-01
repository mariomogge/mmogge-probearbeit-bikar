<?php

namespace App\Controller\Api;

use App\Domain\User\User;
use App\Infrastructure\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{   
    /**
     * Route for registering a new user account
     * 
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $em
     * @param UserRepository $users
     * 
     * @return JsonResponse
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
        UserRepository $users
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true) ?? [];
        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;

        $violations = $validator->validate($email, [new Assert\NotBlank(), new Assert\Email()]);
        $violations->addAll($validator->validate($password, [new Assert\NotBlank(), new Assert\Length(min: 8)]));
        if (0 !== count($violations)) {
            return $this->json(['errors' => (string) $violations], 400);
        }
        if ($users->findOneBy(['email' => strtolower($email)])) {
            return $this->json(['error' => 'Email already registered'], 400);
        }

        $user = new User($email);
        $user->setPassword($hasher->hashPassword($user, $password));


        $em->persist($user);
        $em->flush();
        return $this->json(['message' => 'Registered'], 201);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles this :)
    }
}
