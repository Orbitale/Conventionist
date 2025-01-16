<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\Table(name: '`table`')]
class Table
{
    use Field\Id { Field\Id::__construct as private generateId; }

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(nullable: true)]
    #[Assert\Type('int')]
    private ?int $maxNumberOfParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\OneToMany(targetEntity: TimeSlot::class, mappedBy: 'table')]
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
                'slot_type' => 'table',
            ],
        ];
    }

    //

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
