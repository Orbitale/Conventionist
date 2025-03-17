<?php

namespace App\Entity;

use App\Repository\AttendeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttendeeRepository::class)]
class Attendee
{
    use Field\Id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank()]
    private ?string $name = '';

    #[ORM\Column(name: 'number_of_attendees', type: Types::INTEGER, nullable: false)]
    #[Assert\NotNull]
    #[Assert\Type('int')]
    private ?int $numberOfAttendees = 1;

    #[ORM\ManyToOne(targetEntity: ScheduledActivity::class)]
    #[ORM\JoinColumn(name: 'scheduled_activity_id', referencedColumnName: 'id', nullable: false)]
    private ScheduledActivity $scheduledActivity;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'registered_by_id', referencedColumnName: 'id', nullable: false)]
    private User $registeredBy;

    // Used by form
    #[Assert\Email(mode: Assert\Email::VALIDATION_MODE_STRICT)]
    #[Assert\NotBlank]
    public ?string $email;

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    public function getNumberOfAttendees(): int
    {
        return $this->numberOfAttendees;
    }

    public function setNumberOfAttendees(?int $numberOfAttendees): void
    {
        $this->numberOfAttendees = $numberOfAttendees ?: 0;
    }

    public function getScheduledActivity(): ScheduledActivity
    {
        return $this->scheduledActivity;
    }

    public function setScheduledActivity(ScheduledActivity $scheduledActivity): void
    {
        $this->scheduledActivity = $scheduledActivity;
    }

    public function getRegisteredBy(): User
    {
        return $this->registeredBy;
    }

    public function setRegisteredBy(User $registeredBy): void
    {
        $this->registeredBy = $registeredBy;
        if (!$this->name) {
            $this->name = $registeredBy->getUsername();
            $this->email = $registeredBy->getEmail();
        }
    }
}
