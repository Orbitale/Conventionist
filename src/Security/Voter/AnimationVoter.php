<?php

namespace App\Security\Voter;

use App\Entity\Animation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AnimationVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VIEW_ANIMATIONS,
    ];

    public const string CAN_VIEW_ANIMATIONS = 'CAN_VIEW_ANIMATIONS';

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

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $roles = $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles()));

        if (\in_array('ROLE_ADMIN', $roles, true)) {
            return true;
        }

        if ($subject instanceof Animation) {
            return $user->isOwnerOf($subject);
        }

        if (
            \in_array('ROLE_VISITOR', $roles, true)
            || \in_array('ROLE_CONFERENCE_ORGANIZER', $roles, true)
        ) {
            return true;
        }

        return false;
    }
}
