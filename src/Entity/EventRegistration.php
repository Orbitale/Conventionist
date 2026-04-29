<?php

namespace App\Entity;

use App\Enum\EventRegistrationStatus;
use App\Repository\EventRegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRegistrationRepository::class)]
#[ORM\Table(name: 'event_registrations')]
#[ORM\UniqueConstraint(name: 'UNIQ_EVENT_REG_USER_EVENT', fields: ['user', 'event'])]
class EventRegistration
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull]
    private Event $event;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 32, enumType: EventRegistrationStatus::class, nullable: false)]
    #[Assert\NotNull]
    private EventRegistrationStatus $status = EventRegistrationStatus::PENDING;

    #[ORM\Column(name: 'registered_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $registeredAt;

    #[ORM\Column(name: 'confirmed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $confirmedAt = null;

    #[ORM\Column(name: 'notes', type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
        $this->registeredAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return \sprintf('%s @ %s', $this->user ?? '', $this->event ?? '');
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

    public function getStatus(): EventRegistrationStatus
    {
        return $this->status;
    }

    public function setStatus(EventRegistrationStatus $status): void
    {
        $this->status = $status;
        if ($status === EventRegistrationStatus::CONFIRMED && $this->confirmedAt === null) {
            $this->confirmedAt = new \DateTimeImmutable();
        }
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registeredAt): void
    {
        $this->registeredAt = $registeredAt;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeImmutable $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function cancel(): void
    {
        $this->status = EventRegistrationStatus::CANCELLED;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
