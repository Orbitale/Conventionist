<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Floor;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class FloorFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '2012505d-807b-48ca-b547-5c396c00399c' => [
                'name' => 'RDC',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            '1625a324-e9ad-4227-8615-b07450dce2fd' => [
                'name' => 'Entresol',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            '9a395ed1-0e0b-4d1f-ba4d-482f52fcc9ac' => [
                'name' => '1e étage',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            'e441630e-8333-471c-ae1e-e59fc7cd08ca' => [
                'name' => '2e étage',
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            '8b4822a8-6f45-4365-9de6-19a7a06cd147' => [
                'name' => 'Ground floor',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            '50fc8f3c-712e-4fc9-8b8d-4dc7522ff2a5' => [
                'name' => 'Exterior tent',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            'cf1b753c-7f4f-42f1-9d18-90d977e465f3' => [
                'name' => '1st floor',
                'venue' => new Ref(Venue::class, 'venue-Custom'),
            ],
            '4f5d7818-ab3f-4a73-bed4-2b6cf2724077' => [
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
