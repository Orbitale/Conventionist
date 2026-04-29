<?php

namespace App\Tests\Entity;

use App\Entity\Organization;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class OrganizationTest extends TestCase
{
    public function testCreatorsBehaviour(): void
    {
        $org = new Organization();
        $org->setName('Acme');

        self::assertSame('Acme', (string) $org);
        self::assertCount(0, $org->getCreators());

        $user = new User();
        $org->addCreator($user);

        self::assertCount(1, $org->getCreators());
        self::assertTrue($org->hasCreator($user));
        self::assertTrue($user->isOwnerOf($org));

        $org->removeCreator($user);
        self::assertCount(0, $org->getCreators());
    }
}
