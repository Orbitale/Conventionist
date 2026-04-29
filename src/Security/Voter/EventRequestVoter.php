<?php

namespace App\Security\Voter;

use App\Entity\EventRequest;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class EventRequestVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CREATE,
        self::VIEW,
        self::REVIEW,
        self::WITHDRAW,
    ];

    public const string CREATE = 'EVENT_REQUEST_CREATE';
    public const string VIEW = 'EVENT_REQUEST_VIEW';
    public const string REVIEW = 'EVENT_REQUEST_REVIEW';
    public const string WITHDRAW = 'EVENT_REQUEST_WITHDRAW';

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

        if (!$subject instanceof EventRequest) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $subject->getUser()->isSameAs($user) || $user->isOwnerOf($subject->getEvent()),
            self::WITHDRAW => $subject->getUser()->isSameAs($user),
            self::REVIEW => $user->isOwnerOf($subject->getEvent()),
            default => false,
        };
    }
}
