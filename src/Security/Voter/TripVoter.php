<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\User\UserRole;
use App\ReadModel\Trip\TripOwnershipAwareInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Trip|TripOwnershipAwareInterface>
 */
final class TripVoter extends Voter
{
    public const string SHOW = 'SHOW';
    public const string EDIT = 'EDIT';
    public const string DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return ($subject instanceof Trip || $subject instanceof TripOwnershipAwareInterface)
            && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * @param Trip|TripOwnershipAwareInterface $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($subject instanceof TripOwnershipAwareInterface) {
            return match ($attribute) {
                self::SHOW => true,
                self::EDIT, self::DELETE => $subject->isOwnedByUser($user) || $user->hasRole(UserRole::Admin),
                default => false,
            };
        }

        return match ($attribute) {
            self::SHOW => true,
            self::EDIT, self::DELETE => $subject->owners->contains($user) || $user->hasRole(UserRole::Admin),
            default => false,
        };
    }
}
