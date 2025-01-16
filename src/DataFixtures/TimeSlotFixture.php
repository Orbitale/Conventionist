<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Table;
use App\Entity\TimeSlot;
use App\Entity\TimeSlotCategory;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class TimeSlotFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    protected function getEntityClass(): string
    {
        return TimeSlot::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'timeslot-';
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
            FloorFixture::class,
            RoomFixture::class,
            TableFixture::class,
        ];
    }

    protected function getObjects(): iterable
    {
        return [
            [
                'id' => '28b98eb2-4fef-4587-9749-25af666c25e0',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(1, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(2, 0),
                'table' => $this->getReference('table-Table face pôle JdR 1', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => '11cca5a1-57f5-408c-bb2d-27cd0631fc5c',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(2, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(3, 0),
                'table' => $this->getReference('table-Table face pôle JdR 1', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => '4ae5a1ed-8c39-4c4e-9f0a-2b4169ecabf1',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(4, 0),
                'table' => $this->getReference('table-Table face pôle JdR 1', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => '1d508edc-2963-4014-b822-32bb771d2245',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(4, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(5, 0),
                'table' => $this->getReference('table-Table face pôle JdR 1', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => 'ed52861f-3cfd-47df-ac1d-ffaedf6910e8',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(6, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(7, 30),
                'table' => $this->getReference('table-Hall jeux 01', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Evening', TimeSlotCategory::class),
            ],
            [
                'id' => 'e466073c-d660-48bb-bff3-61bd2a957a2c',
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'table' => $this->getReference('table-Public', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => '29f08a4f-4c31-4735-9280-3eb103df1b9a',
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'table' => $this->getReference('table-Public', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
            [
                'id' => '58a6fd03-9767-4828-a068-eaed48545b92',
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(8, 0),
                'table' => $this->getReference('table-Table proto 1', Table::class),
                'event' => $this->getReference('event-TDC 2025', Event::class),
                'category' => $this->getReference('time-slot-category-Morning', TimeSlotCategory::class),
            ],
        ];
    }
}
