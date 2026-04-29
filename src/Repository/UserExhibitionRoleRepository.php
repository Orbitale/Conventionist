<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\UserExhibitionRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserExhibitionRole>
 */
final class UserExhibitionRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserExhibitionRole::class);
    }

    /**
     * @return array<UserExhibitionRole>
     */
    public function findByUserAndEvent(User $user, Event $event): array
    {
        return $this->findBy(['user' => $user, 'event' => $event]);
    }

    /**
     * @return array<UserExhibitionRole>
     */
    public function findByEvent(Event $event): array
    {
        return $this->findBy(['event' => $event]);
    }
}
