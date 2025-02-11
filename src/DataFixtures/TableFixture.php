<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Room;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;
use Symfony\Component\Uid\Uuid;

class TableFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            Uuid::v7()->toString() => [
                'name' => 'Table face pôle JdR 1',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Table face pôle JdR 2',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Table proto 1',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Table proto 2',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Salle musique',
                'maxNumberOfParticipants' => 250,
                'room' => new Ref(Room::class, 'room-Salle musique'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Public',
                'maxNumberOfParticipants' => 280,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Participants scène',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 01',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 02',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 03',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 04',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 05',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 06',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 07',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 08',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 09',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            Uuid::v7()->toString() => [
                'name' => 'Hall jeux 10',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Table::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'table-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            RoomFixture::class,
        ];
    }
}
