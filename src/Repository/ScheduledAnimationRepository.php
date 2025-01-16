<?php

namespace App\Repository;

use App\Entity\ScheduledAnimation;
use App\Enum\ScheduleAnimationState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduledAnimation>
 */
class ScheduledAnimationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduledAnimation::class);
    }

    public function hasSimilar(ScheduledAnimation $scheduledAnimation): int
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT count(scheduled_animation) amount
            FROM {$this->getEntityName()} scheduled_animation
            WHERE scheduled_animation.id != :id
            AND scheduled_animation.timeSlot = :time_slot
            AND scheduled_animation.state = :accepted
        DQL
        )
            ->setParameter('id', $scheduledAnimation->getId())
            ->setParameter('time_slot', $scheduledAnimation->getTimeSlot())
            ->setParameter('accepted', ScheduleAnimationState::ACCEPTED)
            ->getSingleScalarResult() > 0;
    }

    public function findAtSameTimeSlot(ScheduledAnimation $animation): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT scheduled_animation
            FROM {$this->getEntityName()} scheduled_animation
            WHERE scheduled_animation.id != :id
            AND scheduled_animation.timeSlot = :time_slot
        DQL
            )
                ->setParameter('id', $animation->getId())
                ->setParameter('time_slot', $animation->getTimeSlot())
                ->getResult();
    }
}
