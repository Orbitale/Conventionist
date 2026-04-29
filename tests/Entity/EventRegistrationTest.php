<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Entity\User;
use App\Enum\EventRegistrationStatus;
use PHPUnit\Framework\TestCase;

final class EventRegistrationTest extends TestCase
{
    public function testSetStatusConfirmedSetsConfirmedAt(): void
    {
        $reg = new EventRegistration();
        $reg->setUser(new User());
        $reg->setEvent(new Event());

        self::assertNull($reg->getConfirmedAt());
        $reg->setStatus(EventRegistrationStatus::CONFIRMED);
        self::assertNotNull($reg->getConfirmedAt());
    }

    public function testCancel(): void
    {
        $reg = new EventRegistration();
        $reg->cancel();
        self::assertSame(EventRegistrationStatus::CANCELLED, $reg->getStatus());
        self::assertFalse($reg->isActive());
    }

    public function testDefaultStatusIsPending(): void
    {
        $reg = new EventRegistration();
        self::assertSame(EventRegistrationStatus::PENDING, $reg->getStatus());
        self::assertTrue($reg->isActive());
    }
}
