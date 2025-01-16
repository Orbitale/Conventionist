<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\TimeSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeSlot>
 */
class TimeSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeSlot::class);
    }

    public function hasOverlap(TimeSlot $timeSlot): bool
    {
        $result = $this->getEntityManager()->createQuery(<<<DQL
            SELECT count(time_slot) as has_overlaps
            FROM {$this->getEntityName()} time_slot
            WHERE time_slot.event = :event
                AND time_slot.startsAt < :end
                AND time_slot.endsAt > :start
                AND time_slot.id != :id
        DQL
        )
            ->setParameter('id', $timeSlot->getId())
            ->setParameter('start', $timeSlot->getStartsAt())
            ->setParameter('end', $timeSlot->getEndsAt())
            ->setParameter('event', $timeSlot->getEvent())
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * @return array<TimeSlot>
     */
    public function findForEvent(Event $event): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT
                time_slot,
                event,
                table,
                scheduled_animation,
                animation
            FROM {$this->getEntityName()} time_slot
            INNER JOIN time_slot.event event
            LEFT JOIN time_slot.table table
            LEFT JOIN time_slot.scheduledAnimations scheduled_animation
            LEFT JOIN scheduled_animation.animation animation
            WHERE time_slot.event = :event
        DQL
        )
            ->setParameter('event', $event)
            ->getResult();
    }
}
