<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\SurfSession;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, SurfSession>
 */
final class SurfSessionVoter extends Voter
{
    public const string VIEW = 'VIEW';
    public const string EDIT = 'EDIT';
    public const string DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return ($subject instanceof SurfSession)
            && \in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true);
    }

    /**
     * @param SurfSession $surfSession
     */
    protected function voteOnAttribute(string $attribute, mixed $surfSession, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $surfSession->user === $user,
            default => false,
        };
    }
}
