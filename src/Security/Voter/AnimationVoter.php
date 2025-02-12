<?php

namespace App\Security\Voter;

use App\Entity\Animation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class AnimationVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_EDIT_ANIMATION,
        self::CAN_DELETE_ANIMATION,
    ];

    public const string CAN_EDIT_ANIMATION = 'CAN_EDIT_ANIMATION';
    public const string CAN_DELETE_ANIMATION = 'CAN_DELETE_ANIMATION';

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

        if ($subject instanceof Animation && $attribute !== self::CAN_DELETE_ANIMATION) {
            return $user->isOwnerOf($subject);
        }

        return false;
    }
}
