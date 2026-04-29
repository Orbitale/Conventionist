<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Entity\User;
use App\Enum\EventRegistrationStatus;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class EventRegistrationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return EventRegistration::class;
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
            UserFixture::class,
        ];
    }

    public static function getStaticData(): array
    {
        return [
            'c2c46af4-8a1f-4a3c-9b9b-1a4f1f00a001' => [
                'user' => new Ref(User::class, 'user-visitor'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'status' => EventRegistrationStatus::CONFIRMED,
                'notes' => 'First registration',
            ],
            'c2c46af4-8a1f-4a3c-9b9b-1a4f1f00a002' => [
                'user' => new Ref(User::class, 'user-ash'),
                'event' => new Ref(Event::class, 'event-Custom event'),
                'status' => EventRegistrationStatus::PENDING,
            ],
        ];
    }
}
