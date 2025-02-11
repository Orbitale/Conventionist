<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\User;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class VenueFixture extends ArrayFixture implements ORMFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return Venue::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'venue-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public static function getStaticData(): array
    {
        return [
            'ae58fc80-cf97-49b4-a7ec-6fba881dd1db' => [
                'name' => 'CPC',
                'address' => 'Puy',
                'creators' => [new Ref(User::class, 'user-conference_organizer')],
            ],
            '5bf81d94-dcec-49cb-a1d4-485b05d0c2d7' => [
                'name' => 'Custom',
                'address' => 'Paris',
                'creators' => [new Ref(User::class, 'user-venue_manager')],
            ],
        ];
    }
}
