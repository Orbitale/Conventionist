<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements HasNestedRelations, HasCreators, HasMapImage
{
    use Field\Id { __construct as generateId; }
    use Field\MapImage;
    use Field\Position;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Floor $floor = null;

    /** @var Collection<Booth> */
    #[ORM\OneToMany(targetEntity: Booth::class, mappedBy: 'room', cascade: ['persist', 'refresh'])]
    #[Assert\Valid]
    private Collection $booths;

    public function __construct()
    {
        $this->generateId();
        $this->booths = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->floor?->__toString().' - '.$this->name;
    }

    public function clone(Floor $fromFloor): self
    {
        $clone = clone $this;
        $clone->generateId();
        $clone->floor = $fromFloor;

        $clone->booths = new ArrayCollection($clone->booths->map(fn (Booth $booth) => $booth->clone($clone))->toArray());

        return $clone;
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->booths as $booth) {
            $booth->setRoom($this);
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

        foreach ($this->booths as $booth) {
            $json['children'][] = $booth->getCalendarResourceJson();
        }

        return $json;
    }

    public function getCreators(): Collection
    {
        return $this->floor->getCreators();
    }

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
        $floor->addRoom($this);
    }

    /**
     * @return Collection<Booth>
     */
    public function getBooths(): Collection
    {
        return $this->booths;
    }

    public function addBooth(Booth $booth): void
    {
        if ($this->booths->contains($booth)) {
            return;
        }

        $this->booths->add($booth);
    }

    public function removeBooth(Booth $booth): void
    {
        if (!$this->booths->contains($booth)) {
            return;
        }

        $this->booths->removeElement($booth);
    }

    public function hasBooth(Booth $booth): bool
    {
        return $this->booths->contains($booth);
    }
}
