<?php

namespace App\Repository;

use App\Entity\EventRegistration;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRegistration>
 */
final class EventRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRegistration::class);
    }

    /**
     * @return array<EventRegistration>
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.event', 'e')->addSelect('e')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('e.startsAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
