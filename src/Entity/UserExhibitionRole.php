<?php

namespace App\Entity;

use App\Enum\ExhibitionRole;
use App\Repository\UserExhibitionRoleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserExhibitionRoleRepository::class)]
#[ORM\Table(name: 'user_exhibition_role')]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_EVENT_ROLE', fields: ['user', 'event', 'role'])]
class UserExhibitionRole
{
    use Field\Id { Field\Id::__construct as private generateId; }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Event $event;

    #[ORM\Column(name: 'role', type: Types::STRING, length: 32, enumType: ExhibitionRole::class, nullable: false)]
    #[Assert\NotNull]
    private ExhibitionRole $role = ExhibitionRole::STAFF;

    #[ORM\Column(name: 'scoped_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $scopedAt;

    public function __construct()
    {
        $this->generateId();
        $this->scopedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return \sprintf('%s @ %s (%s)', $this->user ?? '', $this->event ?? '', $this->role->value);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    public function getRole(): ExhibitionRole
    {
        return $this->role;
    }

    public function setRole(ExhibitionRole $role): void
    {
        $this->role = $role;
    }

    public function getScopedAt(): \DateTimeImmutable
    {
        return $this->scopedAt;
    }

    public function setScopedAt(\DateTimeImmutable $scopedAt): void
    {
        $this->scopedAt = $scopedAt;
    }
}
