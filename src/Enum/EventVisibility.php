<?php

namespace App\Enum;

enum EventVisibility: string
{
    case DRAFT = 'draft';
    case PRIVATE = 'private';
    case PUBLIC = 'public';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PRIVATE => 'Private',
            self::PUBLIC => 'Public',
        };
    }

    public function getCssClass(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::PRIVATE => 'warning text-white',
            self::PUBLIC => 'success',
        };
    }
}
