<?php

namespace App\Enum;

enum SafetyToolType: string
{
    case X_CARD = 'x_card';
    case LINES_VEILS = 'lines_veils';
    case OPEN_DOOR = 'open_door';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::X_CARD => 'X-Card',
            self::LINES_VEILS => 'Lines & Veils',
            self::OPEN_DOOR => 'Open Door',
            self::OTHER => 'Other',
        };
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::X_CARD => 'danger',
            self::LINES_VEILS => 'warning text-white',
            self::OPEN_DOOR => 'success',
            self::OTHER => 'secondary',
        };
    }
}
