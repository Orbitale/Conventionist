<?php

namespace App\Security\Voter;

use App\Entity\TimeSlot;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class TimeSlotVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_CREATE_TIME_SLOTS_FOR_EVENT,
        self::CAN_DELETE_TIMESLOT,
    ];

    public const string CAN_CREATE_TIME_SLOTS_FOR_EVENT = 'CAN_CREATE_TIME_SLOTS_FOR_EVENT';
    public const string CAN_DELETE_TIMESLOT = 'CAN_DELETE_TIMESLOT';

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

        if ($subject instanceof TimeSlot && $attribute !== self::CAN_DELETE_TIMESLOT) {
            return $user->isOwnerOf($subject);
        }

        return false;
    }
}
