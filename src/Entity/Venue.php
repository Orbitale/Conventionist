<?php

namespace App\Entity;

use App\Repository\VenueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VenueRepository::class)]
class Venue implements HasNestedRelations, HasCreators, HasMapImage
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Address;
    use Field\GenericContact;
    use Field\Timestampable;
    use Field\MapImage;
    use TimestampableEntity;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    /** @var Collection<Floor> */
    #[ORM\OneToMany(targetEntity: Floor::class, mappedBy: 'venue', cascade: ['persist', 'refresh'])]
    #[Assert\Valid]
    private Collection $floors;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->floors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function clone(): self
    {
        $clone = clone $this;

        $clone->generateId();

        $clone->floors = new ArrayCollection($clone->floors->map(fn (Floor $floor) => $floor->clone($clone))->toArray());

        $clone->creators = new ArrayCollection($clone->creators->toArray());
        $clone->createdAt = clone $clone->createdAt;
        $clone->updatedAt = clone $clone->updatedAt;

        return $clone;
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->floors as $floor) {
            $floor->setVenue($this);
            $floor->refreshNestedRelations();
        }
    }

    public function getChildren(): array
    {
        return $this->floors->toArray();
    }

    public function getChildrenClass(): ?string
    {
        return Floor::class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    /**
     * @return Collection<Floor>
     */
    public function getFloors(): Collection
    {
        return $this->floors;
    }

    public function addFloor(Floor $floor): void
    {
        if ($this->floors->contains($floor)) {
            return;
        }

        $this->floors->add($floor);
    }

    public function removeFloor(Floor $floor): void
    {
        if (!$this->floors->contains($floor)) {
            return;
        }

        $this->floors->removeElement($floor);
    }

    public function hasFloor(Floor $floor): bool
    {
        return $this->floors->contains($floor);
    }
}
