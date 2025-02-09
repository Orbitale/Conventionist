<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class EventFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return Event::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'event-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            VenueFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            'b715276f-f7df-42ee-82f8-c21b05d2da2d' => [
                'name' => 'TDC 2025',
                'startsAt' => new \DateTimeImmutable('10 days')->setTime(0, 0, 0, 0),
                'endsAt' => new \DateTimeImmutable('12 days')->setTime(0, 0, 0, 0),
                'address' => 'CPC',
                'description' => 'Hello world',
                'creators' => [new Ref(User::class, 'user-admin'), new Ref(User::class, 'user-conference_organizer')],
                'enabled' => true,
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
        ];
    }
}
