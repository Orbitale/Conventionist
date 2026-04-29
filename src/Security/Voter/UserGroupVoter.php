<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Enum\UserGroupRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class UserGroupVoter extends Voter
{
    public const string CAN_EDIT_USER_GROUP = 'CAN_EDIT_USER_GROUP';
    public const string CAN_DELETE_USER_GROUP = 'CAN_DELETE_USER_GROUP';
    public const string CAN_MANAGE_MEMBERS = 'CAN_MANAGE_MEMBERS';

    public const array PERMISSIONS = [
        self::CAN_EDIT_USER_GROUP,
        self::CAN_DELETE_USER_GROUP,
        self::CAN_MANAGE_MEMBERS,
    ];

    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true) && $subject instanceof UserGroup;
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

        if (!$subject instanceof UserGroup) {
            return false;
        }

        if ($user->isOwnerOf($subject)) {
            return true;
        }

        foreach ($subject->getMemberships() as $membership) {
            if ($membership->getUser()->getId() !== $user->getId()) {
                continue;
            }

            $role = $membership->getRole();

            if ($role === UserGroupRole::OWNER) {
                return true;
            }

            if ($role === UserGroupRole::LEADER && $attribute === self::CAN_MANAGE_MEMBERS) {
                return true;
            }
        }

        return false;
    }
}
