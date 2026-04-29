<?php

namespace App\Entity;

use App\Enum\UserGroupRole;
use App\Repository\UserGroupMembershipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserGroupMembershipRepository::class)]
#[ORM\Table(name: 'user_group_membership')]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_GROUP_MEMBERSHIP', fields: ['user', 'userGroup'])]
class UserGroupMembership
{
    use Field\Id { Field\Id::__construct as private generateId; }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: UserGroup::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private UserGroup $userGroup;

    #[ORM\Column(name: 'role', type: Types::STRING, length: 32, enumType: UserGroupRole::class, nullable: false)]
    #[Assert\NotNull]
    private UserGroupRole $role = UserGroupRole::MEMBER;

    #[ORM\Column(name: 'joined_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $joinedAt;

    public function __construct()
    {
        $this->generateId();
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return \sprintf('%s @ %s (%s)', $this->user ?? '', $this->userGroup ?? '', $this->role->value);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    public function getRole(): UserGroupRole
    {
        return $this->role;
    }

    public function setRole(UserGroupRole $role): void
    {
        $this->role = $role;
    }

    public function getJoinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): void
    {
        $this->joinedAt = $joinedAt;
    }
}
