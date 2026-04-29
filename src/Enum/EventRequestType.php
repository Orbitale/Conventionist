<?php

namespace App\Enum;

enum EventRequestType: string
{
    case PARTNER = 'partner';
    case ORGANIZER = 'organizer';
    case STAFF = 'staff';

    public function getLabel(): string
    {
        return match ($this) {
            self::PARTNER => 'Partner',
            self::ORGANIZER => 'Organizer',
            self::STAFF => 'Staff',
        };
    }
}
