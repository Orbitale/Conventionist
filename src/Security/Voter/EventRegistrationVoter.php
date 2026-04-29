<?php

namespace App\Security\Voter;

use App\Entity\EventRegistration;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class EventRegistrationVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CREATE,
        self::VIEW,
        self::CANCEL,
    ];

    public const string CREATE = 'EVENT_REGISTRATION_CREATE';
    public const string VIEW = 'EVENT_REGISTRATION_VIEW';
    public const string CANCEL = 'EVENT_REGISTRATION_CANCEL';

    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (\in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles())), true)) {
            return true;
        }

        if ($attribute === self::CREATE) {
            return true;
        }

        if (!$subject instanceof EventRegistration) {
            return false;
        }

        return match ($attribute) {
            self::VIEW, self::CANCEL => $subject->getUser()->isSameAs($user)
                || $user->isOwnerOf($subject->getEvent()),
            default => false,
        };
    }
}
