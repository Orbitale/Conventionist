<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
final class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return array<int, Notification>
     */
    public function findForRecipient(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('n.readAt', 'ASC')
            ->addOrderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadForRecipient(User $user): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.recipient = :user')
            ->andWhere('n.readAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllAsReadFor(User $user, ?\DateTimeImmutable $at = null): int
    {
        return (int) $this->createQueryBuilder('n')
            ->update()
            ->set('n.readAt', ':at')
            ->where('n.recipient = :user')
            ->andWhere('n.readAt IS NULL')
            ->setParameter('at', $at ?? new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
