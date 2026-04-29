<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ScheduledActivity;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Enum\EventRegistrationStatus;
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
            WHERE scheduled_activity.activity != :activity
            AND scheduled_activity.timeSlot = :time_slot
        DQL
        )
            ->setParameter('activity', $activity->getActivity())
            ->setParameter('time_slot', $activity->getTimeSlot())
            ->getResult();
    }

    /**
     * Advanced public search over scheduled activities.
     *
     * @param array<string, mixed> $filters Supported keys:
     *                                      event (string id), game (string id), category (string id),
     *                                      theme (string id), state (ScheduleActivityState value),
     *                                      from (date string), to (date string),
     *                                      seats ("any"|"available"), q (free-text)
     *
     * @return array{items: list<ScheduledActivity>, total: int}
     *
     * Note on the "available seats" filter:
     *   Counting confirmed registrations per scheduled activity is expensive and the
     *   current data model does not link EventRegistration to ScheduledActivity.
     *   As an approximation we treat a session as "available" when either:
     *     - capacity is null (uncapped), OR
     *     - waitlist is enabled (implying the organizer still accepts signups).
     *   This is intentionally loose; a future revision should count confirmed
     *   registrations directly once that relation exists.
     */
    public function searchPaginated(array $filters, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $qb = $this->createQueryBuilder('sa')
            ->leftJoin('sa.activity', 'activity')->addSelect('activity')
            ->leftJoin('sa.timeSlot', 'ts')->addSelect('ts')
            ->leftJoin('ts.event', 'event')->addSelect('event')
            ->leftJoin('sa.game', 'game')->addSelect('game')
            ->leftJoin('game.categories', 'category')
            ->leftJoin('game.themes', 'theme')
            ->orderBy('ts.startsAt', 'ASC');

        if (!empty($filters['event'])) {
            $qb->andWhere('event.id = :eventId')->setParameter('eventId', $filters['event']);
        }

        if (!empty($filters['game'])) {
            $qb->andWhere('game.id = :gameId')->setParameter('gameId', $filters['game']);
        }

        if (!empty($filters['category'])) {
            $qb->andWhere('category.id = :categoryId')->setParameter('categoryId', $filters['category']);
        }

        if (!empty($filters['theme'])) {
            $qb->andWhere('theme.id = :themeId')->setParameter('themeId', $filters['theme']);
        }

        if (!empty($filters['state']) && $filters['state'] instanceof ScheduleActivityState) {
            $qb->andWhere('sa.state = :state')->setParameter('state', $filters['state']);
        }

        if (!empty($filters['from']) && $filters['from'] instanceof \DateTimeInterface) {
            $qb->andWhere('ts.startsAt >= :from')->setParameter('from', $filters['from']);
        }

        if (!empty($filters['to']) && $filters['to'] instanceof \DateTimeInterface) {
            $qb->andWhere('ts.endsAt <= :to')->setParameter('to', $filters['to']);
        }

        if (!empty($filters['seats']) && $filters['seats'] === 'available') {
            // Approximation: uncapped or waitlist-enabled sessions are considered available.
            $qb->andWhere('(sa.capacity IS NULL OR sa.waitlistEnabled = true)');
        }

        if (!empty($filters['q'])) {
            $qb->andWhere('(LOWER(game.title) LIKE :q OR LOWER(activity.name) LIKE :q OR LOWER(event.name) LIKE :q)')
                ->setParameter('q', '%'.\mb_strtolower((string) $filters['q']).'%');
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT sa.id)')
            ->resetDQLPart('orderBy');
        // Remove addSelects added via joins for count.
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $items = $qb->getQuery()->getResult();

        return ['items' => $items, 'total' => $total];
    }

    /**
     * Upcoming sessions where the user is either:
     *   (a) a registered (active) attendee of the session's parent Event, or
     *   (b) the submitter (GM/creator) of the scheduled activity.
     *
     * Note: the schema does not expose a direct session-level attendee link here, so
     * attendance is approximated at the parent-Event level.
     *
     * @return array<ScheduledActivity>
     */
    public function findUpcomingForUser(User $user, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT scheduled_activity, time_slot, activity, event
            FROM {$this->getEntityName()} scheduled_activity
            INNER JOIN scheduled_activity.timeSlot time_slot
            INNER JOIN time_slot.event event
            LEFT JOIN scheduled_activity.activity activity
            WHERE time_slot.startsAt >= :from
              AND time_slot.startsAt <= :to
              AND (
                scheduled_activity.submittedBy = :user
                OR EXISTS (
                    SELECT 1 FROM App\\Entity\\EventRegistration reg
                    WHERE reg.user = :user
                      AND reg.event = event
                      AND reg.status IN (:active_statuses)
                )
              )
            ORDER BY time_slot.startsAt ASC
        DQL
        )
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('active_statuses', [
                EventRegistrationStatus::CONFIRMED,
                EventRegistrationStatus::PENDING,
                EventRegistrationStatus::WAITLIST,
                EventRegistrationStatus::CHECKED_IN,
            ])
            ->getResult();
    }

    /**
     * Find ScheduledActivity rows whose grace deadline has passed and whose
     * GM never checked in, still sitting in AWAITING_GM.
     *
     * @return array<ScheduledActivity>
     */
    public function findStaleAwaitingGm(\DateTimeImmutable $now): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT scheduled_activity
            FROM {$this->getEntityName()} scheduled_activity
            WHERE scheduled_activity.state = :state
              AND scheduled_activity.graceDeadlineAt IS NOT NULL
              AND scheduled_activity.graceDeadlineAt < :now
              AND scheduled_activity.gmCheckedInAt IS NULL
        DQL
        )
            ->setParameter('state', ScheduleActivityState::AWAITING_GM)
            ->setParameter('now', $now)
            ->getResult();
    }

    public function hasSimilarForUser(User $user, Activity $activity, TimeSlot $slot): bool
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT count(scheduled_activity) as count
            FROM {$this->getEntityName()} scheduled_activity
            WHERE scheduled_activity.activity = :activity
            AND scheduled_activity.timeSlot = :time_slot
            AND scheduled_activity.submittedBy = :user
        DQL
        )
            ->setParameter('user', $user)
            ->setParameter('activity', $activity)
            ->setParameter('time_slot', $slot)
            ->getSingleScalarResult() > 0;
    }
}
