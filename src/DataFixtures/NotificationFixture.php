<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationLevel;
use App\Enum\NotificationType;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class NotificationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '0194a0f0-0001-7000-8000-000000000001' => [
                'recipient' => new Ref(User::class, 'user-visitor'),
                'type' => NotificationType::SYSTEM,
                'level' => NotificationLevel::INFO,
                'title' => 'Welcome to Conventionist',
                'body' => 'Thanks for joining. Check out upcoming events!',
                'link' => null,
                'readAt' => null,
                'metadata' => null,
            ],
            '0194a0f0-0001-7000-8000-000000000002' => [
                'recipient' => new Ref(User::class, 'user-visitor'),
                'type' => NotificationType::REGISTRATION_STATUS,
                'level' => NotificationLevel::SUCCESS,
                'title' => 'Registration confirmed',
                'body' => 'Your registration for an activity has been confirmed.',
                'link' => null,
                'readAt' => null,
                'metadata' => ['activity' => 'visitor-activity'],
            ],
            '0194a0f0-0001-7000-8000-000000000003' => [
                'recipient' => new Ref(User::class, 'user-visitor'),
                'type' => NotificationType::EVENT_UPDATE,
                'level' => NotificationLevel::WARNING,
                'title' => 'Event schedule updated',
                'body' => 'An event you are registered to changed its schedule.',
                'link' => null,
                'readAt' => new \DateTimeImmutable('-1 day'),
                'metadata' => null,
            ],
            '0194a0f0-0001-7000-8000-000000000004' => [
                'recipient' => new Ref(User::class, 'user-admin'),
                'type' => NotificationType::REQUEST_REVIEWED,
                'level' => NotificationLevel::INFO,
                'title' => 'New activity submitted',
                'body' => 'A new activity was submitted for review.',
                'link' => null,
                'readAt' => null,
                'metadata' => null,
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Notification::class;
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
