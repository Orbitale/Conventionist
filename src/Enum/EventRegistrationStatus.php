<?php

namespace App\Enum;

enum EventRegistrationStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case WAITLIST = 'waitlist';
    case CHECKED_IN = 'checked_in';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => '#ffc107',
            self::CONFIRMED => '#198754',
            self::CANCELLED => '#dc3545',
            self::WAITLIST => '#6c757d',
            self::CHECKED_IN => '#0d6efd',
        };
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::PENDING => 'warning text-white',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
            self::WAITLIST => 'secondary',
            self::CHECKED_IN => 'primary',
        };
    }

    public function isActive(): bool
    {
        return match ($this) {
            self::CONFIRMED, self::CHECKED_IN, self::PENDING, self::WAITLIST => true,
            self::CANCELLED => false,
        };
    }
}
