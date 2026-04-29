<?php

namespace App\Enum;

enum GameComplexity: string
{
    case LIGHT = 'light';
    case MEDIUM_LIGHT = 'medium_light';
    case MEDIUM = 'medium';
    case MEDIUM_HEAVY = 'medium_heavy';
    case HEAVY = 'heavy';

    public function getLabel(): string
    {
        return match ($this) {
            self::LIGHT => 'Light',
            self::MEDIUM_LIGHT => 'Medium-light',
            self::MEDIUM => 'Medium',
            self::MEDIUM_HEAVY => 'Medium-heavy',
            self::HEAVY => 'Heavy',
        };
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::LIGHT => 'success',
            self::MEDIUM_LIGHT => 'info',
            self::MEDIUM => 'secondary',
            self::MEDIUM_HEAVY => 'warning text-white',
            self::HEAVY => 'danger',
        };
    }
}
