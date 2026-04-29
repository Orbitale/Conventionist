<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupMembership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserGroupMembership>
 */
final class UserGroupMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroupMembership::class);
    }

    public function findOneByUserAndGroup(User $user, UserGroup $group): ?UserGroupMembership
    {
        return $this->findOneBy(['user' => $user, 'userGroup' => $group]);
    }

    /**
     * @return array<UserGroupMembership>
     */
    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }
}
