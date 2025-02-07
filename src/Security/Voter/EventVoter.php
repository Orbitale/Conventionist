<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    public const array PERMISSIONS = [
        self::CAN_VIEW_EVENTS,
        self::CAN_VIEW_SINGLE_EVENT,
        self::CAN_EDIT_EVENT,
        self::CAN_DELETE_EVENT,
    ];

    public const string CAN_VIEW_EVENTS = 'CAN_VIEW_EVENTS';
    public const string CAN_VIEW_SINGLE_EVENT = 'CAN_VIEW_SINGLE_EVENT';
    public const string CAN_EDIT_EVENT = 'CAN_EDIT_EVENT';
    public const string CAN_DELETE_EVENT = 'CAN_DELETE_EVENT';

    public function __construct(private readonly AuthorizationCheckerInterface $authChecker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, self::PERMISSIONS, true) && (!$subject || $subject instanceof Event);
    }

    /**
     * @param null|Event $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (
            $attribute === self::CAN_DELETE_EVENT
            || $attribute === self::CAN_EDIT_EVENT
        ) {
            if (!$subject) {
                return false;
            }

            return $user->isOwnerOf($subject);
        }

        return $this->authChecker->isGranted([
            'ROLE_ADMIN',
            'ROLE_VISITOR',
            'ROLE_CONFERENCE_ORGANIZER',
        ]);
    }
}
