<?php

namespace App\DataFixtures;

use App\DataFixtures\Tools\GetObjectsFromData;
use App\DataFixtures\Tools\Ref;
use App\Entity\Animation;
use App\Entity\ScheduledAnimation;
use App\Entity\TimeSlot;
use App\Enum\ScheduleAnimationState;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class ScheduledAnimationFixture extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    use GetObjectsFromData;

    public static function getStaticData(): array
    {
        return [
            '5d079782-f013-4a81-9223-45670d4e102b' => [
                'animation' => new Ref(Animation::class, 'animation-Animation de jeu'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-28b98eb2-4fef-4587-9749-25af666c25e0'),
                'state' => ScheduleAnimationState::CREATED,
            ],
            '449c8f33-1dc8-44d6-a9f3-d90aa2e06740' => [
                'animation' => new Ref(Animation::class, 'animation-Animation de jeu'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-11cca5a1-57f5-408c-bb2d-27cd0631fc5c'),
                'state' => ScheduleAnimationState::PENDING_REVIEW,
            ],
            '6a6ab381-d2ab-4d54-9c70-c4beb58b5dce' => [
                'animation' => new Ref(Animation::class, 'animation-Visitor animation'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-11cca5a1-57f5-408c-bb2d-27cd0631fc5c'),
                'state' => ScheduleAnimationState::PENDING_REVIEW,
            ],
            '27b0dc33-d685-45db-aad6-4162f8bccc94' => [
                'animation' => new Ref(Animation::class, 'animation-Visitor animation'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-4ae5a1ed-8c39-4c4e-9f0a-2b4169ecabf1'),
                'state' => ScheduleAnimationState::REJECTED,
            ],
            'f166e2cf-0f87-4aff-90d8-1a7466480238' => [
                'animation' => new Ref(Animation::class, 'animation-Visitor animation'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-1d508edc-2963-4014-b822-32bb771d2245'),
                'state' => ScheduleAnimationState::ACCEPTED,
            ],
            'e0c60354-7a9f-4a5a-93b2-66f6892008e9' => [
                'animation' => new Ref(Animation::class, 'animation-Concert'),
                'timeSlot' => new Ref(TimeSlot::class, 'timeslot-29f08a4f-4c31-4735-9280-3eb103df1b9a'),
                'state' => ScheduleAnimationState::ACCEPTED,
            ],
        ];
    }

    protected function getEntityClass(): string
    {
        return ScheduledAnimation::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'scheduled-animation-';
    }

    public function getDependencies(): array
    {
        return [
            AnimationFixture::class,
            TimeSlotFixture::class,
        ];
    }
}
