<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements HasNestedRelations
{
    use Field\Id { __construct as generateId; }

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Floor $floor = null;

    /** @var Collection<Table> */
    #[ORM\OneToMany(targetEntity: Table::class, mappedBy: 'room', cascade: ['persist', 'refresh'])]
    #[Assert\Valid]
    private Collection $tables;

    public function __construct()
    {
        $this->generateId();
        $this->tables = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->floor?->__toString().' - '.$this->name;
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->tables as $table) {
            $table->setRoom($this);
        }
    }

    public function getCalendarResourceJson(): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->name,
            'children' => [],
            'extendedProps' => [
                'slot_type' => 'room',
            ],
        ];

        foreach ($this->tables as $table) {
            $json['children'][] = $table->getCalendarResourceJson();
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

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(Floor $floor): void
    {
        $this->floor = $floor;
    }

    /**
     * @return Collection<Table>
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): void
    {
        if ($this->tables->contains($table)) {
            return;
        }

        $this->tables->add($table);
    }

    public function removeTable(Table $table): void
    {
        if (!$this->tables->contains($table)) {
            return;
        }

        $this->tables->removeElement($table);
    }

    public function hasTable(Table $table): bool
    {
        return $this->tables->contains($table);
    }

    /**
     * @return array<TimeSlot>
     */
    public function getTimeSlots(): array
    {
        $slots = [];

        foreach ($this->tables as $table) {
            foreach ($table->getTimeSlots() as $slot) {
                $slots[$slot->getId()] = $slot;
            }
        }

        return \array_values($slots);
    }
}
