<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class TimeSlotVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VIEW_TIMESLOTS,
        self::CAN_CREATE_TIME_SLOTS_FOR_EVENT,
    ];

    public const string CAN_VIEW_TIMESLOTS = 'CAN_VIEW_TIMESLOTS';
    public const string CAN_CREATE_TIME_SLOTS_FOR_EVENT = 'CAN_CREATE_TIME_SLOTS_FOR_EVENT';

    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS);
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

        if ($subject instanceof Event) {
            return $user->isOwnerOf($subject);
        }

        if ($attribute === self::CAN_VIEW_TIMESLOTS) {
            return \in_array('ROLE_CONFERENCE_ORGANIZER', $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles())), true);
        }

        return false;
    }
}
