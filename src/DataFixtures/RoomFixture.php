<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Floor;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class RoomFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '66d67ca4-3ca9-4e34-aec6-4ee0e7fc6469' => [
                'name' => 'Hall principal',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            '3bb3cb09-3849-41d2-a326-aafc266a6911' => [
                'name' => 'Hall jeux',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            '1613d07d-ad17-4196-bee8-682569192644' => [
                'name' => 'Salle musique',
                'floor' => new Ref(Floor::class, 'floor-RDC'),
            ],
            '2b5ec274-1537-4b25-a666-cce60ee2ffa3' => [
                'name' => 'Salle entresol',
                'floor' => new Ref(Floor::class, 'floor-Entresol'),
            ],
            'c7e59b12-8d0c-4312-934d-eeb1e5e3adc4' => [
                'name' => 'Scène',
                'floor' => new Ref(Floor::class, 'floor-Entresol'),
            ],
            '4c1b1ef2-43f8-4aaa-8215-400a5e7d5c8a' => [
                'name' => 'Custom Entry hall',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            '8fbdb6b0-a1de-45a4-9485-4cb1daeee91d' => [
                'name' => 'Custom Hall 1',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            'd15540a4-1038-4da6-ac2e-6920bca71598' => [
                'name' => 'Custom Hall 2',
                'floor' => new Ref(Floor::class, 'floor-Ground floor'),
            ],
            'd0de1415-49fb-4e9e-8d81-22aa59276240' => [
                'name' => 'Custom Tent',
                'floor' => new Ref(Floor::class, 'floor-Exterior tent'),
            ],
            '45ea8b7f-2d51-45c8-8e69-27d98310d85f' => [
                'name' => 'Custom Room 10',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            '8f9277a3-80fc-43e2-af79-409f8b991fb2' => [
                'name' => 'Custom Room 11',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
            ],
            'dbe9de9e-e330-4d40-a2ef-7db3debaea17' => [
                'name' => 'Custom Room 12',
                'floor' => new Ref(Floor::class, 'floor-1st floor'),
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
