<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class EventFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return Event::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'event-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            VenueFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            '322c94a8-ab40-41c6-b272-d605d03e068f' => [
                'name' => 'TDC 2025',
                'slug' => 'tdc-2025',
                'startsAt' => (new \DateTimeImmutable('+100 days'))->setTime(8, 0, 0),
                'endsAt' => (new \DateTimeImmutable('+101 days'))->setTime(20, 0, 0),
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nAliquam vel diam a lorem convallis mattis sit amet non metus. Pellentesque purus nibh, egestas eu dolor ut, scelerisque viverra erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.\n\nNunc vitae dictum dolor, in tristique odio.\n\nEtiam ante ipsum, molestie nec hendrerit id, tincidunt aliquam sapien.\n\nNullam tempus fringilla auctor. Fusce elementum et neque a congue.\nProin molestie sapien a fringilla lobortis.\n\nMorbi non erat ipsum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Proin eu elit non nulla vulputate mattis. Aenean pretium, sapien sed tempus posuere, ipsum dolor semper nisl, ac dictum purus lectus a nisi.\nVestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae",
                'creators' => [new Ref(User::class, 'user-ash')],
                'published' => true,
                'venue' => new Ref(Venue::class, 'venue-CPC'),
            ],
            '36b1b285-ec34-49e4-b462-93a313995cc9' => [
                'name' => 'Custom event',
                'slug' => 'custom-event',
                'startsAt' => (new \DateTimeImmutable('+200 days'))->setTime(8, 0, 0),
                'endsAt' => (new \DateTimeImmutable('+201 days'))->setTime(20, 0, 0),
                'description' => "Etiam posuere ex justo, a mattis risus laoreet at.\n\nInteger non lectus a quam malesuada aliquam.\n\nSuspendisse ultricies nulla sit amet odio tempus, quis volutpat erat feugiat. Phasellus odio lacus, scelerisque id sollicitudin eu, accumsan ut nulla. Etiam sodales tempor neque nec elementum.\nAliquam erat volutpat. In quis dui sagittis, vestibulum felis a, porta erat. Nulla tincidunt, nisl et vulputate viverra, enim lacus posuere dui, et placerat mi velit eu metus.\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\nMauris pulvinar odio lectus.",
                'creators' => [new Ref(User::class, 'user-visitor')],
                'published' => true,
                'venue' => new Ref(Venue::class, 'venue-Custom'),
                'allowActivityRegistration' => false,
                'allowAttendeeRegistration' => false,
            ],
        ];
    }
}
