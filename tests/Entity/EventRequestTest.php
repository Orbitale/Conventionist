<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use App\Entity\EventRequest;
use App\Entity\User;
use App\Enum\EventRequestStatus;
use PHPUnit\Framework\TestCase;

final class EventRequestTest extends TestCase
{
    public function testReviewSetsFields(): void
    {
        $req = new EventRequest();
        $req->setUser(new User());
        $req->setEvent(new Event());

        $reviewer = new User();
        $req->review($reviewer, EventRequestStatus::APPROVED);

        self::assertSame($reviewer, $req->getReviewedBy());
        self::assertNotNull($req->getReviewedAt());
        self::assertSame(EventRequestStatus::APPROVED, $req->getStatus());
    }

    public function testWithdraw(): void
    {
        $req = new EventRequest();
        $req->withdraw();
        self::assertSame(EventRequestStatus::WITHDRAWN, $req->getStatus());
    }
}
