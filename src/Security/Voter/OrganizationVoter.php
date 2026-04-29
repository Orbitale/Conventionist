<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class OrganizationVoter extends Voter
{
    public const string CAN_EDIT_ORGANIZATION = 'CAN_EDIT_ORGANIZATION';
    public const string CAN_DELETE_ORGANIZATION = 'CAN_DELETE_ORGANIZATION';

    public const array PERMISSIONS = [
        self::CAN_EDIT_ORGANIZATION,
        self::CAN_DELETE_ORGANIZATION,
    ];

    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true) && $subject instanceof Organization;
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

        if ($subject instanceof Organization && $attribute === self::CAN_EDIT_ORGANIZATION) {
            return $user->isOwnerOf($subject);
        }

        return false;
    }
}
