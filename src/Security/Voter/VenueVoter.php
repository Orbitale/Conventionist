<?php

namespace App\Security\Voter;

use App\Entity\Booth;
use App\Entity\Floor;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Venue;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class VenueVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VIEW_VENUES,
        self::CAN_DELETE_VENUE,
    ];

    public const string CAN_VIEW_VENUES = 'CAN_VIEW_VENUES';
    public const string CAN_DELETE_VENUE = 'CAN_DELETE_VENUE';

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

        $roles = $this->roleHierarchy->getReachableRoleNames(\array_merge($token->getRoleNames(), $user->getRoles()));

        if (\in_array('ROLE_ADMIN', $roles, true)) {
            return true;
        }

        if (
            $attribute !== self::CAN_DELETE_VENUE
            && (
                $subject instanceof Venue
                || $subject instanceof Floor
                || $subject instanceof Room
                || $subject instanceof Booth
            )
        ) {
            return $user->isOwnerOf($subject);
        }

        return false;
    }
}
