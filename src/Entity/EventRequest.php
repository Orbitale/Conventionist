<?php

namespace App\Entity;

use App\Enum\EventRequestStatus;
use App\Enum\EventRequestType;
use App\Repository\EventRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRequestRepository::class)]
#[ORM\Table(name: 'event_requests')]
class EventRequest
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

    #[ORM\Column(name: 'request_type', type: Types::STRING, length: 32, enumType: EventRequestType::class, nullable: false)]
    #[Assert\NotNull]
    private EventRequestType $requestType = EventRequestType::PARTNER;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 32, enumType: EventRequestStatus::class, nullable: false)]
    #[Assert\NotNull]
    private EventRequestStatus $status = EventRequestStatus::SUBMITTED;

    #[ORM\Column(name: 'message', type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(name: 'reviewed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reviewed_by_id', referencedColumnName: 'id', nullable: true)]
    private ?User $reviewedBy = null;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }

    public function __toString(): string
    {
        return \sprintf('%s request for %s', $this->requestType->value, $this->event ?? '');
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

    public function getRequestType(): EventRequestType
    {
        return $this->requestType;
    }

    public function setRequestType(EventRequestType $requestType): void
    {
        $this->requestType = $requestType;
    }

    public function getStatus(): EventRequestStatus
    {
        return $this->status;
    }

    public function setStatus(EventRequestStatus $status): void
    {
        $this->status = $status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getReviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): void
    {
        $this->reviewedAt = $reviewedAt;
    }

    public function getReviewedBy(): ?User
    {
        return $this->reviewedBy;
    }

    public function setReviewedBy(?User $reviewedBy): void
    {
        $this->reviewedBy = $reviewedBy;
    }

    public function review(User $reviewer, EventRequestStatus $status): void
    {
        $this->reviewedBy = $reviewer;
        $this->reviewedAt = new \DateTimeImmutable();
        $this->status = $status;
    }

    public function withdraw(): void
    {
        $this->status = EventRequestStatus::WITHDRAWN;
    }
}
