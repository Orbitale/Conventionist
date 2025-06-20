<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Booth;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class BoothFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '89bda66f-5a81-46c5-af15-554107fc8766' => [
                'name' => 'Table face pôle JdR 1',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
                'availableEquipment' => [
                    'table' => 2,
                    'chairs' => 6,
                ],
            ],
            'c38cf5f3-a003-42f0-ba95-90160aee52c7' => [
                'name' => 'Table face pôle JdR 2',
                'maxNumberOfParticipants' => 5,
                'room' => new Ref(Room::class, 'room-Hall principal'),
            ],
            'c63002e0-1443-4834-ba48-df10ba6ad513' => [
                'name' => 'Table proto 1',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            '8f78c289-66e8-4fc7-be7f-f0580bf5dcba' => [
                'name' => 'Table proto 2',
                'maxNumberOfParticipants' => 4,
                'room' => new Ref(Room::class, 'room-Salle entresol'),
            ],
            'fdba95d8-ab0f-43d2-b6a9-474376ea3023' => [
                'name' => 'Salle musique',
                'maxNumberOfParticipants' => 250,
                'room' => new Ref(Room::class, 'room-Salle musique'),
            ],
            '51c6cc9c-f4e4-4a49-9869-50309106cf44' => [
                'name' => 'Public',
                'maxNumberOfParticipants' => 280,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            '8270020d-8d7e-4021-943d-3cb49b6da7a9' => [
                'name' => 'Participants scène',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Scène'),
            ],
            '957ce373-a181-481a-819c-e5ecd30faa24' => [
                'name' => 'Hall jeux 01',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '87a53b82-cb3c-455f-8c5b-3763f4b150fa' => [
                'name' => 'Hall jeux 02',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '16770e01-f7bc-43c6-926c-0b6cca46e397' => [
                'name' => 'Hall jeux 03',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'aef45ceb-2683-4f3c-a7e1-5b161bace7c8' => [
                'name' => 'Hall jeux 04',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '71737597-5249-4b8c-8db9-e5d8f2c02340' => [
                'name' => 'Hall jeux 05',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            '88ff29c7-a98f-40b8-8502-3d968fee5e0c' => [
                'name' => 'Stand désactivé',
                'maxNumberOfParticipants' => 6,
                'room' => new Ref(Room::class, 'room-Hall jeux'),
            ],
            'c04a5f37-84e8-4dd6-9a87-f504ff79b0a8' => [
                'name' => 'Custom Main hall table 1',
                'maxNumberOfParticipants' => 6,
                'allowAttendeeRegistration' => false,
                'room' => new Ref(Room::class, 'room-Custom Entry hall'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Booth::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'booth-';
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
