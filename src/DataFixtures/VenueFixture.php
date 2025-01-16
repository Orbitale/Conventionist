<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
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
            ],
        ];
    }
}
