<?php

namespace App\Enum;

enum ScheduleActivityState: string
{
    case CREATED = 'created';
    case PENDING_REVIEW = 'pending_review';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';
    case WAITLIST_OPEN = 'waitlist_open';
    case REGISTRATION_CLOSED = 'registration_closed';
    case AWAITING_GM = 'awaiting_gm';
    case CANCELLED_NO_GM = 'cancelled_no_gm';

    public function getColor(): string
    {
        return self::getStateColor($this);
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::CREATED => 'secondary',
            self::PENDING_REVIEW => 'warning text-white',
            self::REJECTED => 'danger',
            self::ACCEPTED => 'success',
            self::WAITLIST_OPEN => 'info text-white',
            self::REGISTRATION_CLOSED => 'dark text-white',
            self::AWAITING_GM => 'warning text-white',
            self::CANCELLED_NO_GM => 'danger',
        };
    }

    public static function getStateColor(self $state): string
    {
        return match ($state) {
            self::CREATED => '#343a40',
            self::PENDING_REVIEW => '#ffc107',
            self::REJECTED => '#dc3545',
            self::ACCEPTED => '#198754',
            self::WAITLIST_OPEN => '#0dcaf0',
            self::REGISTRATION_CLOSED => '#212529',
            self::AWAITING_GM => '#fd7e14',
            self::CANCELLED_NO_GM => '#b02a37',
        };
    }
}
