<?php

namespace App\Enum;

enum UserGroupRole: string
{
    case MEMBER = 'member';
    case LEADER = 'leader';
    case OWNER = 'owner';

    public function getLabel(): string
    {
        return match ($this) {
            self::MEMBER => 'Member',
            self::LEADER => 'Leader',
            self::OWNER => 'Owner',
        };
    }
}
