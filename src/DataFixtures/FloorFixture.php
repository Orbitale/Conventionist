<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Floor;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class FloorFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '82038d5d-165e-44b3-9938-f286b6544298' => [
                'name' => 'RDC',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            'c97f718d-fd46-4d5d-80eb-7e52dee9b470' => [
                'name' => 'Entresol',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
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
