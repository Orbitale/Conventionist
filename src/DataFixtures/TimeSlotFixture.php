<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Event;
use App\Entity\Table;
use App\Entity\TimeSlot;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class TimeSlotFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

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

    public static function getStaticData(): array
    {
        return [
            '28b98eb2-4fef-4587-9749-25af666c25e0' => [
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(1, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(2, 0),
                'table' => new Ref(Table::class, 'table-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            '11cca5a1-57f5-408c-bb2d-27cd0631fc5c' => [
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(2, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(3, 0),
                'table' => new Ref(Table::class, 'table-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            '4ae5a1ed-8c39-4c4e-9f0a-2b4169ecabf1' => [
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(4, 0),
                'table' => new Ref(Table::class, 'table-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            '1d508edc-2963-4014-b822-32bb771d2245' => [
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(4, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(5, 0),
                'table' => new Ref(Table::class, 'table-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            'ed52861f-3cfd-47df-ac1d-ffaedf6910e8' => [
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(6, 0),
                'endsAt' => new \DateTimeImmutable('10 days')->setTime(7, 30),
                'table' => new Ref(Table::class, 'table-Hall jeux 01'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            'e466073c-d660-48bb-bff3-61bd2a957a2c' => [
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'table' => new Ref(Table::class, 'table-Public'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            '29f08a4f-4c31-4735-9280-3eb103df1b9a' => [
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(3, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'table' => new Ref(Table::class, 'table-Public'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
            '58a6fd03-9767-4828-a068-eaed48545b92' => [
                'startsAt' => new \DateTimeImmutable('11 days')->setTime(7, 0),
                'endsAt' => new \DateTimeImmutable('11 days')->setTime(8, 0),
                'table' => new Ref(Table::class, 'table-Table proto 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
            ],
        ];
    }
}
