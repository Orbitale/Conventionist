<?php

namespace App\Entity;

use App\Enum\NotificationLevel;
use App\Enum\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notification')]
#[ORM\Index(name: 'idx_notification_recipient_read', columns: ['recipient_id', 'read_at'])]
class Notification
{
    use Field\Id { __construct as generateId; }
    use Field\Timestampable;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_id', referencedColumnName: 'id', nullable: false)]
    private User $recipient;

    #[ORM\Column(name: 'type', type: Types::STRING, length: 32, nullable: false, enumType: NotificationType::class)]
    private NotificationType $type = NotificationType::SYSTEM;

    #[ORM\Column(name: 'level', type: Types::STRING, length: 16, nullable: false, enumType: NotificationLevel::class)]
    private NotificationLevel $level = NotificationLevel::INFO;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $title = '';

    #[ORM\Column(name: 'body', type: Types::TEXT, nullable: false)]
    private string $body = '';

    #[ORM\Column(name: 'link', type: Types::STRING, length: 512, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(name: 'read_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    /** @var array<string, mixed>|null */
    #[ORM\Column(name: 'metadata', type: Types::JSON, nullable: true)]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getRecipient(): User
    {
        return $this->recipient;
    }

    public function setRecipient(User $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function setType(NotificationType $type): void
    {
        $this->type = $type;
    }

    public function getLevel(): NotificationLevel
    {
        return $this->level;
    }

    public function setLevel(NotificationLevel $level): void
    {
        $this->level = $level;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): void
    {
        $this->readAt = $readAt;
    }

    public function isRead(): bool
    {
        return $this->readAt !== null;
    }

    public function markAsRead(?\DateTimeImmutable $at = null): void
    {
        if ($this->readAt === null) {
            $this->readAt = $at ?? new \DateTimeImmutable();
        }
    }

    /** @return array<string, mixed>|null */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /** @param array<string, mixed>|null $metadata */
    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
