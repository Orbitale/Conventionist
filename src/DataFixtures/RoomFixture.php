<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Floor;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\Uid\Uuid;

class RoomFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            Uuid::v7()->toString() => [
                'name' => 'Hall principal',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Salle musique',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Salle entresol',
                'floor' => new Ref(Floor::class, 'floor-Entresol'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Scène',
                'floor' => new Ref(Floor::class, 'floor-Entresol'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Entry hall',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall 1',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall 2',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Tent',
                'floor' => new Ref(Floor::class, 'floor-Exterior tent'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 10',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 11',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 12',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 13',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 20',
                'floor' => new Ref(Floor::class, 'floor-2nd floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 21',
                'floor' => new Ref(Floor::class, 'floor-2nd floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 22',
                'floor' => new Ref(Floor::class, 'floor-2nd floor'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Room 23',
                'floor' => new Ref(Floor::class, 'floor-2nd floor'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Room::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'room-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            FloorFixture::class,
        ];
    }
}
