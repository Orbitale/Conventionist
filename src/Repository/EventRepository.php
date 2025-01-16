<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findUpcoming()
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT event
            FROM {$this->getEntityName()} event
            WHERE event.startsAt >= :start
        DQL
        )
            ->setParameter('start', (new \DateTimeImmutable('yesterday'))->setTime(0, 0))
            ->getResult();
    }

    public function findForCalendar(string $eventId): ?Event
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT event,
                venue,
                floor,
                room,
                table,
                time_slot
            FROM {$this->getEntityName()} event
            INNER JOIN event.venue venue
            LEFT JOIN venue.floors floor
            LEFT JOIN floor.rooms room
            LEFT JOIN room.tables table
            LEFT JOIN table.timeSlots time_slot
            WHERE event.id = :id
            ORDER BY table.name ASC,
                room.name ASC,
                floor.name ASC
        DQL
        )
            ->setParameter('id', $eventId)
            ->getOneOrNullResult();
    }
}
