<?php

namespace App\Enum;

enum ScheduleAnimationState: string
{
    case CREATED = 'created';
    case PENDING_REVIEW = 'pending_review';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';

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
        };
    }

    public static function getStateColor(self $state): string
    {
        return match ($state) {
            self::CREATED => '#343a40',
            self::PENDING_REVIEW => '#ffc107',
            self::REJECTED => '#dc3545',
            self::ACCEPTED => '#198754',
        };
    }
}
