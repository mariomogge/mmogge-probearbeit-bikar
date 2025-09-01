<?php

namespace App\Application\Security;

use App\Domain\Account\Account;
use App\Domain\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

// Voter checks whether current user is owner of an account
// Restricts access to accounts owners only
final class AccountVoter extends Voter
{
    public const VIEW = 'ACCOUNT_VIEW';
    public const OPERATE = 'ACCOUNT_OPERATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::OPERATE], true) && $subject instanceof Account;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var Account $account */
        $account = $subject;
        return $account->getOwner()->getId() === $user->getId();
    }
}
