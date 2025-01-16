<?php

namespace App\Entity;

use App\Repository\FloorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FloorRepository::class)]
class Floor implements HasNestedRelations
{
    use Field\Id { __construct as generateId; }

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\ManyToOne(inversedBy: 'floors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Venue $venue = null;

    /** @var Collection<Room> */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'floor', cascade: ['persist', 'refresh'])]
    #[Assert\Valid]
    private Collection $rooms;

    public function __construct()
    {
        $this->generateId();
        $this->rooms = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->venue?->__toString().' - '.$this->name;
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->rooms as $room) {
            $room->setFloor($this);
            $room->refreshNestedRelations();
        }
    }

    public function getCalendarResourceJson(): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->name,
            'children' => [],
            'extendedProps' => [
                'slot_type' => 'floor',
            ],
        ];

        foreach ($this->rooms as $room) {
            $json['children'][] = $room->getCalendarResourceJson();
        }

        return $json;
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

    public function getVenue(): ?Venue
    {
        return $this->venue;
    }

    /**
     * @return Collection<Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function setVenue(Venue $venue): void
    {
        $this->venue = $venue;
    }

    public function addRoom(Room $room): void
    {
        if ($this->rooms->contains($room)) {
            return;
        }

        $this->rooms->add($room);
    }

    public function removeRoom(Room $room): void
    {
        if (!$this->rooms->contains($room)) {
            return;
        }

        $this->rooms->removeElement($room);
    }

    public function hasRoom(Room $room): bool
    {
        return $this->rooms->contains($room);
    }
}
