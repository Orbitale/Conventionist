<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
final class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findUpcoming(?User $user)
    {
        $qb = $this->createQueryBuilder('event')
            ->where('event.startsAt >= :start')
            ->setParameter('start', (new \DateTimeImmutable('yesterday'))->setTime(0, 0));

        if ($user) {
            $qb->innerJoin('event.creators', 'creators')
                ->addSelect('creators')
                ->andWhere('creators IN (:creator)')
                ->setParameter('creator', $user)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findForCalendar(string $eventId): ?Event
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT event,
                venue,
                floor,
                room,
                booth,
                time_slot
            FROM {$this->getEntityName()} event
            INNER JOIN event.venue venue
            LEFT JOIN venue.floors floor
            LEFT JOIN floor.rooms room
            LEFT JOIN room.booths booth
            LEFT JOIN booth.timeSlots time_slot
            WHERE event.id = :id
            ORDER BY booth.name ASC,
                room.name ASC,
                floor.name ASC
        DQL
        )
            ->setParameter('id', $eventId)
            ->getOneOrNullResult();
    }

    public function findOneWithRelations(string $slug): ?Event
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT event,
                venue,
                floor,
                room,
                booth,
                time_slot,
                scheduled_activity,
                activity
            FROM {$this->getEntityName()} event
            INNER JOIN event.venue venue
            LEFT JOIN venue.floors floor
            LEFT JOIN floor.rooms room
            LEFT JOIN room.booths booth
            LEFT JOIN booth.timeSlots time_slot
            LEFT JOIN time_slot.scheduledActivities scheduled_activity
            LEFT JOIN scheduled_activity.activity activity
            WHERE event.slug = :slug
            ORDER BY booth.name ASC,
                room.name ASC,
                floor.name ASC
        DQL
        )
            ->setParameter('slug', $slug)
            ->getOneOrNullResult();
    }

}
