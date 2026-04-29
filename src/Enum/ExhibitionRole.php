<?php

namespace App\Enum;

enum ExhibitionRole: string
{
    case ORGANIZER = 'organizer';
    case PARTNER = 'partner';
    case STAFF = 'staff';

    public function getLabel(): string
    {
        return match ($this) {
            self::ORGANIZER => 'Organizer',
            self::PARTNER => 'Partner',
            self::STAFF => 'Staff',
        };
    }
}
