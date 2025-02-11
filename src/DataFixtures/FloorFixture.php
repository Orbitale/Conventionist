<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Floor;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\Uid\Uuid;

class FloorFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            Uuid::v7()->toString() => [
                'name' => 'RDC',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Entresol',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            Uuid::v7()->toString() => [
                'name' => '1e étage',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            Uuid::v7()->toString() => [
                'name' => '2e étage',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Ground floor',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Exterior tent',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            Uuid::v7()->toString() => [
                'name' => '1st floor',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            Uuid::v7()->toString() => [
                'name' => '2nd floor',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Floor::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'floor-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
        ];
    }
}
