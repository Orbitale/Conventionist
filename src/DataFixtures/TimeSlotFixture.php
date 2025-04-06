<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Booth;
use App\Entity\Event;
use App\Entity\TimeSlot;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class TimeSlotFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
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
            BoothFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            '28b98eb2-4fef-4587-9749-25af666c25e0' => [
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(1, 0),
                'endsAt' => (new \DateTimeImmutable('+100 days'))->setTime(2, 0),
                'booth' => new Ref(Booth::class, 'booth-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '11cca5a1-57f5-408c-bb2d-27cd0631fc5c' => [
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(2, 0),
                'endsAt' => (new \DateTimeImmutable('+100 days'))->setTime(3, 0),
                'booth' => new Ref(Booth::class, 'booth-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '4ae5a1ed-8c39-4c4e-9f0a-2b4169ecabf1' => [
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(3, 0),
                'endsAt' => (new \DateTimeImmutable('+100 days'))->setTime(4, 0),
                'booth' => new Ref(Booth::class, 'booth-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '1d508edc-2963-4014-b822-32bb771d2245' => [
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(4, 0),
                'endsAt' => (new \DateTimeImmutable('+100 days'))->setTime(5, 0),
                'booth' => new Ref(Booth::class, 'booth-Table face pôle JdR 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            'ed52861f-3cfd-47df-ac1d-ffaedf6910e8' => [
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(6, 0),
                'endsAt' => (new \DateTimeImmutable('+100 days'))->setTime(7, 30),
                'booth' => new Ref(Booth::class, 'booth-Hall jeux 01'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            'e466073c-d660-48bb-bff3-61bd2a957a2c' => [
                'startsAt' => (new \DateTimeImmutable('+101 days'))->setTime(3, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(7, 0),
                'booth' => new Ref(Booth::class, 'booth-Public'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '29f08a4f-4c31-4735-9280-3eb103df1b9a' => [
                'startsAt' => (new \DateTimeImmutable('+101 days'))->setTime(3, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(7, 0),
                'booth' => new Ref(Booth::class, 'booth-Public'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '58a6fd03-9767-4828-a068-eaed48545b92' => [
                'startsAt' => (new \DateTimeImmutable('+101 days'))->setTime(7, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(8, 0),
                'booth' => new Ref(Booth::class, 'booth-Table proto 1'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => true,
            ],
            '1646ae7d-5414-44e8-89a2-c56a12808172' => [
                'startsAt' => (new \DateTimeImmutable('+101 days'))->setTime(7, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(9, 0),
                'booth' => new Ref(Booth::class, 'booth-Hall jeux 01'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => false,
            ],
            'cf377cb5-31b4-421f-85b6-31c44086aef3' => [
                'startsAt' => (new \DateTimeImmutable('+101 days'))->setTime(10, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(11, 0),
                'booth' => new Ref(Booth::class, 'booth-Hall jeux 01'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'open' => false,
            ],
        ];
    }
}
