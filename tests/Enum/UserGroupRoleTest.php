<?php

namespace App\Tests\Enum;

use App\Enum\ExhibitionRole;
use App\Enum\UserGroupRole;
use PHPUnit\Framework\TestCase;

final class UserGroupRoleTest extends TestCase
{
    public function testUserGroupRoleCases(): void
    {
        self::assertSame('member', UserGroupRole::MEMBER->value);
        self::assertSame('leader', UserGroupRole::LEADER->value);
        self::assertSame('owner', UserGroupRole::OWNER->value);
        self::assertSame('Owner', UserGroupRole::OWNER->getLabel());
    }

    public function testExhibitionRoleCases(): void
    {
        self::assertSame('organizer', ExhibitionRole::ORGANIZER->value);
        self::assertSame('partner', ExhibitionRole::PARTNER->value);
        self::assertSame('staff', ExhibitionRole::STAFF->value);
        self::assertSame('Staff', ExhibitionRole::STAFF->getLabel());
    }
}
