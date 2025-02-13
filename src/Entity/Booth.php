<?php

namespace App\Entity;

use App\Repository\BoothRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BoothRepository::class)]
#[ORM\Table(name: '`booth`')]
class Booth implements HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(nullable: true)]
    #[Assert\Type('int')]
    private ?int $maxNumberOfParticipants = null;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'booths')]
    #[ORM\JoinColumn(name: 'room_id', nullable: false)]
    private ?Room $room = null;

    #[ORM\OneToMany(targetEntity: TimeSlot::class, mappedBy: 'booth')]
    private Collection $timeSlots;

    public function __construct()
    {
        $this->generateId();
        $this->timeSlots = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->room?->__toString().' - '.$this->name;
    }

    public function getCalendarResourceJson(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'extendedProps' => [
                'slot_type' => 'booth',
            ],
        ];
    }

    public function getCreators(): Collection
    {
        return $this->room->getCreators();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    public function getMaxNumberOfParticipants(): ?int
    {
        return $this->maxNumberOfParticipants;
    }

    public function setMaxNumberOfParticipants(?int $maxNumberOfParticipants): void
    {
        $this->maxNumberOfParticipants = $maxNumberOfParticipants;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): void
    {
        $this->room = $room;
    }

    /**
     * @return Collection<TimeSlot>
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }
}
