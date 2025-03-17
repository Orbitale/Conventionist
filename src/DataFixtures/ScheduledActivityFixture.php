<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Activity;
use App\Entity\ScheduledActivity;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Enum\ScheduleActivityState;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class ScheduledActivityFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '5d079782-f013-4a81-9223-45670d4e102b' => [
                'activity' => new Ref(Activity::class, 'activity-Activité de jeu'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-28b98eb2-4fef-4587-9749-25af666c25e0'),
                'state' => ScheduleActivityState::CREATED,
                'submittedBy' => new Ref(User::class, 'user-admin'),
            ],
            '449c8f33-1dc8-44d6-a9f3-d90aa2e06740' => [
                'activity' => new Ref(Activity::class, 'activity-Activité de jeu'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-11cca5a1-57f5-408c-bb2d-27cd0631fc5c'),
                'state' => ScheduleActivityState::PENDING_REVIEW,
                'submittedBy' => new Ref(User::class, 'user-admin'),
            ],
            '6a6ab381-d2ab-4d54-9c70-c4beb58b5dce' => [
                'activity' => new Ref(Activity::class, 'activity-Visitor activity'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-11cca5a1-57f5-408c-bb2d-27cd0631fc5c'),
                'state' => ScheduleActivityState::PENDING_REVIEW,
                'submittedBy' => new Ref(User::class, 'user-visitor'),
            ],
            '27b0dc33-d685-45db-aad6-4162f8bccc94' => [
                'activity' => new Ref(Activity::class, 'activity-Visitor activity'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-4ae5a1ed-8c39-4c4e-9f0a-2b4169ecabf1'),
                'state' => ScheduleActivityState::REJECTED,
                'submittedBy' => new Ref(User::class, 'user-visitor'),
            ],
            'f166e2cf-0f87-4aff-90d8-1a7466480238' => [
                'activity' => new Ref(Activity::class, 'activity-Visitor activity'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-1d508edc-2963-4014-b822-32bb771d2245'),
                'state' => ScheduleActivityState::ACCEPTED,
                'submittedBy' => new Ref(User::class, 'user-visitor'),
            ],
            'e0c60354-7a9f-4a5a-93b2-66f6892008e9' => [
                'activity' => new Ref(Activity::class, 'activity-Concert'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-29f08a4f-4c31-4735-9280-3eb103df1b9a'),
                'state' => ScheduleActivityState::ACCEPTED,
                'submittedBy' => new Ref(User::class, 'user-visitor'),
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return ScheduledActivity::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'scheduled-activity-';
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixture::class,
            TimeSlotFixture::class,
            UserFixture::class,
        ];
    }
}
