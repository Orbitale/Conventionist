<?php

namespace App\Tests\Entity;

use App\Entity\Organization;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupMembership;
use App\Enum\UserGroupRole;
use PHPUnit\Framework\TestCase;

final class UserGroupTest extends TestCase
{
    public function testMembership(): void
    {
        $org = new Organization();
        $org->setName('Acme');

        $group = new UserGroup();
        $group->setName('Staff');
        $group->setOrganization($org);

        $user = new User();

        $membership = new UserGroupMembership();
        $membership->setUser($user);
        $membership->setRole(UserGroupRole::LEADER);
        $group->addMembership($membership);

        self::assertSame($group, $membership->getUserGroup());
        self::assertCount(1, $group->getMemberships());
        self::assertSame(UserGroupRole::LEADER, $membership->getRole());
        self::assertSame($org, $group->getOrganization());
        self::assertInstanceOf(\DateTimeImmutable::class, $membership->getJoinedAt());
    }
}
