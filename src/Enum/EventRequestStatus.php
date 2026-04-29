<?php

namespace App\Enum;

enum EventRequestStatus: string
{
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function getColor(): string
    {
        return match ($this) {
            self::SUBMITTED => '#ffc107',
            self::APPROVED => '#198754',
            self::REJECTED => '#dc3545',
            self::WITHDRAWN => '#6c757d',
        };
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::SUBMITTED => 'warning text-white',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::WITHDRAWN => 'secondary',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::APPROVED, self::REJECTED, self::WITHDRAWN => true,
            self::SUBMITTED => false,
        };
    }
}
