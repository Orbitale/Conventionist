<?php

namespace App\Enum;

enum NotificationType: string
{
    case EVENT_UPDATE = 'event_update';
    case REGISTRATION_STATUS = 'registration_status';
    case BOOKING_UPDATE = 'booking_update';
    case GROUP_INVITE = 'group_invite';
    case REQUEST_REVIEWED = 'request_reviewed';
    case SYSTEM = 'system';

    public function getLabel(): string
    {
        return match ($this) {
            self::EVENT_UPDATE => 'Event update',
            self::REGISTRATION_STATUS => 'Registration status',
            self::BOOKING_UPDATE => 'Booking update',
            self::GROUP_INVITE => 'Group invite',
            self::REQUEST_REVIEWED => 'Request reviewed',
            self::SYSTEM => 'System',
        };
    }
}
