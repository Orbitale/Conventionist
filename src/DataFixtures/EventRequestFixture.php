<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Event;
use App\Entity\EventRequest;
use App\Entity\User;
use App\Enum\EventRequestStatus;
use App\Enum\EventRequestType;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class EventRequestFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    protected function getEntityClass(): string
    {
        return EventRequest::class;
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
            'd3d46af4-8a1f-4a3c-9b9b-1a4f1f00b001' => [
                'user' => new Ref(User::class, 'user-visitor'),
                'event' => new Ref(Event::class, 'event-TDC 2025'),
                'requestType' => EventRequestType::PARTNER,
                'status' => EventRequestStatus::SUBMITTED,
                'message' => 'I would love to be a partner.',
            ],
        ];
    }
}
