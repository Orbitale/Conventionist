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
        self::CAN_VIEW_SCHEDULES,
    ];

    public const string CAN_VALIDATE_SCHEDULE = 'CAN_VALIDATE_SCHEDULE';
    public const string CAN_VIEW_SCHEDULES = 'CAN_VIEW_SCHEDULES';

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

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $roles = $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles()));

        if (\in_array('ROLE_ADMIN', $roles, true)) {
            return true;
        }

        if ($subject instanceof ScheduledAnimation) {
            if (!$subject->canChangeState()) {
                return false;
            }

            return $user->isOwnerOf($subject->getEvent());
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
