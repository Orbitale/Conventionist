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
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(name: 'number_of_attendees', type: Types::INTEGER, nullable: false)]
    private int $numberOfAttendees = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'scheduled_animation_id', referencedColumnName: 'id', nullable: false)]
    private ScheduledAnimation $scheduledAnimation;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'registered_by_id', referencedColumnName: 'id', nullable: false)]
    private User $registeredBy;

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

    public function getScheduledAnimation(): ScheduledAnimation
    {
        return $this->scheduledAnimation;
    }

    public function setScheduledAnimation(ScheduledAnimation $scheduledAnimation): void
    {
        $this->scheduledAnimation = $scheduledAnimation;
    }

    public function getRegisteredBy(): User
    {
        return $this->registeredBy;
    }

    public function setRegisteredBy(User $registeredBy): void
    {
        $this->registeredBy = $registeredBy;
    }
}
