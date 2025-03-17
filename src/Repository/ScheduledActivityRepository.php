<?php

namespace App\Repository;

use App\Entity\ScheduledActivity;
use App\Enum\ScheduleActivityState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduledActivity>
 */
final class ScheduledActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduledActivity::class);
    }

    public function hasSimilar(ScheduledActivity $scheduledActivity): int
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT count(scheduled_activity) amount
            FROM {$this->getEntityName()} scheduled_activity
            WHERE scheduled_activity.id != :id
            AND scheduled_activity.timeSlot = :time_slot
            AND scheduled_activity.state = :accepted
        DQL
        )
            ->setParameter('id', $scheduledActivity->getId())
            ->setParameter('time_slot', $scheduledActivity->getTimeSlot())
            ->setParameter('accepted', ScheduleActivityState::ACCEPTED)
            ->getSingleScalarResult() > 0;
    }

    public function findAtSameTimeSlot(ScheduledActivity $activity): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT scheduled_activity
            FROM {$this->getEntityName()} scheduled_activity
            WHERE scheduled_activity.id != :id
            AND scheduled_activity.timeSlot = :time_slot
        DQL
        )
            ->setParameter('id', $activity->getId())
            ->setParameter('time_slot', $activity->getTimeSlot())
            ->getResult();
    }
}
