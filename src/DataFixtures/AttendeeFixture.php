<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Attendee;
use App\Entity\ScheduledActivity;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class AttendeeFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '7f10eb24-e82d-4f0d-890a-c9cda56b0905' => [
                'name' => 'Alex Doe',
                'numberOfAttendees' => 1,
                'scheduledActivity' => new Ref(ScheduledActivity::class, 'scheduled-activity-f166e2cf-0f87-4aff-90d8-1a7466480238'),
                'registeredBy' => new Ref(User::class, 'user-visitor'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return Attendee::class;
    }

    public function getDependencies(): array
    {
        return [
            ScheduledActivityFixture::class,
            UserFixture::class,
        ];
    }
}
