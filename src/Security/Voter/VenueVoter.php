<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Venue;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VenueVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VIEW_VENUES,
        self::CAN_EDIT_VENUE,
        self::CAN_DELETE_VENUE,
    ];

    public const string CAN_VIEW_VENUES = 'CAN_VIEW_VENUES';
    public const string CAN_EDIT_VENUE = 'CAN_EDIT_VENUE';
    public const string CAN_DELETE_VENUE = 'CAN_DELETE_VENUE';

    public function __construct(private readonly AuthorizationCheckerInterface $authChecker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->authChecker->isGranted(['ROLE_ADMIN'])) {
            return true;
        }

        if (
            ($attribute === self::CAN_DELETE_VENUE
            || $attribute === self::CAN_EDIT_VENUE)
            && $subject instanceof Venue
        ) {
            return $user->isOwnerOf($subject);
        }

        return false;
    }
}
