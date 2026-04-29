<?php

namespace App\Tests\Service\Waitlist;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Enum\EventRegistrationStatus;
use App\Repository\EventRegistrationRepository;
use App\Service\Waitlist\WaitlistPromoter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class WaitlistPromoterTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private EventRegistrationRepository&MockObject $repository;
    private WaitlistPromoter $promoter;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EventRegistrationRepository::class);
        $this->promoter = new WaitlistPromoter($this->em, $this->repository, null);
    }

    public function testNoOpWhenWaitlistDisabled(): void
    {
        $event = $this->createEvent(capacity: 10, waitlistEnabled: false);

        $this->repository->expects($this->never())->method('findBy');
        $this->em->expects($this->never())->method('flush');

        self::assertSame([], $this->promoter->promoteForEvent($event));
    }

    public function testNoOpWhenCapacityNull(): void
    {
        $event = $this->createEvent(capacity: null, waitlistEnabled: true);

        $this->repository->expects($this->never())->method('findBy');
        $this->em->expects($this->never())->method('flush');

        self::assertSame([], $this->promoter->promoteForEvent($event));
    }

    public function testPromotesSingleWaitlistWhenOneSeatFrees(): void
    {
        $event = $this->createEvent(capacity: 2, waitlistEnabled: true);

        $occupying = [$this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01'))];
        $waitlistReg = $this->createRegistration(EventRegistrationStatus::WAITLIST, new \DateTimeImmutable('2026-01-02'));

        $this->repository->method('findBy')->willReturnCallback(
            function (array $criteria, ?array $orderBy = null, ?int $limit = null) use ($event, $occupying, $waitlistReg) {
                self::assertSame($event, $criteria['event']);
                if ($criteria['status'] === EventRegistrationStatus::WAITLIST) {
                    self::assertSame(['registeredAt' => 'ASC'], $orderBy);
                    self::assertSame(1, $limit);

                    return [$waitlistReg];
                }

                return $occupying;
            },
        );

        $this->em->expects($this->once())->method('flush');

        $result = $this->promoter->promoteForEvent($event);

        self::assertCount(1, $result);
        self::assertSame(EventRegistrationStatus::CONFIRMED, $waitlistReg->getStatus());
        self::assertNotNull($waitlistReg->getConfirmedAt());
    }

    public function testPromotesMultipleFifoOrdered(): void
    {
        $event = $this->createEvent(capacity: 5, waitlistEnabled: true);

        $occupying = [
            $this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01')),
            $this->createRegistration(EventRegistrationStatus::CHECKED_IN, new \DateTimeImmutable('2026-01-01')),
        ];

        $w1 = $this->createRegistration(EventRegistrationStatus::WAITLIST, new \DateTimeImmutable('2026-01-02 10:00'));
        $w2 = $this->createRegistration(EventRegistrationStatus::WAITLIST, new \DateTimeImmutable('2026-01-02 11:00'));
        $w3 = $this->createRegistration(EventRegistrationStatus::WAITLIST, new \DateTimeImmutable('2026-01-02 12:00'));

        $this->repository->method('findBy')->willReturnCallback(
            function (array $criteria, ?array $orderBy = null, ?int $limit = null) use ($occupying, $w1, $w2, $w3) {
                if ($criteria['status'] === EventRegistrationStatus::WAITLIST) {
                    self::assertSame(3, $limit);
                    self::assertSame(['registeredAt' => 'ASC'], $orderBy);

                    return [$w1, $w2, $w3];
                }

                return $occupying;
            },
        );

        $this->em->expects($this->once())->method('flush');

        $result = $this->promoter->promoteForEvent($event);

        self::assertSame([$w1, $w2, $w3], $result);
        foreach ([$w1, $w2, $w3] as $reg) {
            self::assertSame(EventRegistrationStatus::CONFIRMED, $reg->getStatus());
        }
    }

    public function testDoesNotOverPromoteBeyondCapacity(): void
    {
        $event = $this->createEvent(capacity: 3, waitlistEnabled: true);

        $occupying = [
            $this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01')),
            $this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01')),
        ];

        // Only 1 seat available; assert limit==1 even with many waitlisted.
        $w1 = $this->createRegistration(EventRegistrationStatus::WAITLIST, new \DateTimeImmutable('2026-01-02 10:00'));

        $limitSeen = null;
        $this->repository->method('findBy')->willReturnCallback(
            function (array $criteria, ?array $orderBy = null, ?int $limit = null) use ($occupying, $w1, &$limitSeen) {
                if ($criteria['status'] === EventRegistrationStatus::WAITLIST) {
                    $limitSeen = $limit;

                    return [$w1];
                }

                return $occupying;
            },
        );

        $result = $this->promoter->promoteForEvent($event);

        self::assertSame(1, $limitSeen);
        self::assertCount(1, $result);
    }

    public function testNoOpWhenCapacityAlreadyFull(): void
    {
        $event = $this->createEvent(capacity: 2, waitlistEnabled: true);

        $occupying = [
            $this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01')),
            $this->createRegistration(EventRegistrationStatus::CONFIRMED, new \DateTimeImmutable('2026-01-01')),
        ];

        $this->repository->expects($this->once())->method('findBy')->willReturn($occupying);
        $this->em->expects($this->never())->method('flush');

        self::assertSame([], $this->promoter->promoteForEvent($event));
    }

    private function createEvent(?int $capacity, bool $waitlistEnabled): Event
    {
        $event = new class extends Event {
            public function __construct()
            {
                // Skip parent constructor to avoid id/venue setup in tests.
            }
        };
        $event->setCapacity($capacity);
        $event->setWaitlistEnabled($waitlistEnabled);

        return $event;
    }

    private function createRegistration(EventRegistrationStatus $status, \DateTimeImmutable $registeredAt): EventRegistration
    {
        $reg = new EventRegistration();
        $reg->setStatus($status);
        $reg->setRegisteredAt($registeredAt);
        // Reset confirmedAt to isolate promotion assertion.
        if ($status !== EventRegistrationStatus::CONFIRMED) {
            $reg->setConfirmedAt(null);
        }

        return $reg;
    }
}
