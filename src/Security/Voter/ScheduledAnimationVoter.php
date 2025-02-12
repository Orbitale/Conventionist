<?php

namespace App\Security\Voter;

use App\Entity\ScheduledAnimation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class ScheduledAnimationVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VALIDATE_SCHEDULE,
        self::CAN_DELETE_SCHEDULED_ANIMATION,
    ];

    public const string CAN_VALIDATE_SCHEDULE = 'CAN_VALIDATE_SCHEDULE';
    public const string CAN_DELETE_SCHEDULED_ANIMATION = 'CAN_DELETE_SCHEDULED_ANIMATION';

    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true);
    }

    /**
     * @param mixed|ScheduledAnimation $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (\in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles())), true)) {
            return true;
        }

        if ($subject instanceof ScheduledAnimation && $attribute !== self::CAN_DELETE_SCHEDULED_ANIMATION) {
            if ($attribute === self::CAN_VALIDATE_SCHEDULE && !$subject->canChangeState()) {
                return false;
            }

            return $user->isOwnerOf($subject->getEvent());
        }

        return false;
    }
}
