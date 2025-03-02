<?php

namespace App\Tests\Entity;

use App\Entity\Booth;
use App\Entity\Event;
use App\Entity\Floor;
use App\Entity\Room;
use App\Entity\ScheduledActivity;
use App\Entity\TimeSlot;
use App\Entity\Venue;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    public function testGetScheduledActivityById(): void
    {
        $venue = new Venue();

        $event = new Event();
        $event->setVenue($venue);

        $floor = new Floor();
        $floor->setVenue($venue);

        $room = new Room();
        $room->setFloor($floor);

        $booth = new Booth();
        $booth->setRoom($room);

        $slot = new TimeSlot();
        $slot->setEvent($event);
        $slot->setBooth($booth);

        $activity = new ScheduledActivity();
        $activity->setTimeSlot($slot);

        try {
            $fetched = $event->getScheduledActivityById($activity->getId());
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }

        self::assertSame($activity, $fetched);
    }
}
