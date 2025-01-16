<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Room;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class TableFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '53f0c904-34a0-4b1b-8e73-3bd2bfcd3dfa' => [
                'name' => 'Table face pôle JdR 1',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
            ],
            'd6d12434-cff2-4b65-be7d-48b525a89839' => [
                'name' => 'Table face pôle JdR 2',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
            ],
            'fed1fa05-728e-403c-8c2f-a3e01c1cd14a' => [
                'name' => 'Table proto 1',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            '6ec3dd38-f4ed-425c-94a1-cb64dc16cfcb' => [
                'name' => 'Table proto 2',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            '4f1b8ba2-73ad-4d65-8396-e91ef4ffa9fb' => [
                'name' => 'Salle musique',
                'maxNumberOfParticipants' => 250,
                'room' => new Ref(Room::class, 'room-Salle musique'),
            ],
            '9f4ece41-25be-4164-9dab-01ad16fd5e73' => [
                'name' => 'Public',
                'maxNumberOfParticipants' => 280,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            '3af5628d-00ea-4c45-a190-53bf84f7d24e' => [
                'name' => 'Participants scène',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            'cb274e83-46bd-4ca8-a9da-0018ce25c0d4' => [
                'name' => 'Hall jeux 01',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'b8804acd-25c5-46aa-af01-9cce33d05fd2' => [
                'name' => 'Hall jeux 02',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'eb3db42b-1c80-4288-bd31-60cf3dcef657' => [
                'name' => 'Hall jeux 03',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '2f06d694-7d1d-4b73-b330-f4c127e22f2d' => [
                'name' => 'Hall jeux 04',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'dcfe73c7-d9b0-4995-a7d7-14d7a127a555' => [
                'name' => 'Hall jeux 05',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'e40ae322-b6eb-4f8e-b332-927202e3ca8d' => [
                'name' => 'Hall jeux 06',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'ce2bf74e-6e30-4d5e-8034-f91fb9efee08' => [
                'name' => 'Hall jeux 07',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '596de25c-fb25-4530-b45a-9623149d464f' => [
                'name' => 'Hall jeux 08',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'b99fb739-048b-455c-a750-46bee9531f35' => [
                'name' => 'Hall jeux 09',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'd973512e-a769-426e-9d2a-987df880515c' => [
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
