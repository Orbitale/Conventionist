<?php

namespace App\Entity;

use App\Repository\VenueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VenueRepository::class)]
class Venue implements HasNestedRelations
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';


    #[ORM\Column(type: Types::TEXT)]
    private string $address = '';

    /** @var Collection<Floor> */
    #[ORM\OneToMany(targetEntity: Floor::class, mappedBy: 'venue', cascade: ['persist', 'refresh'])]
    #[Assert\Valid]
    private Collection $floors;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->floors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->floors as $floor) {
            $floor->setVenue($this);
            $floor->refreshNestedRelations();
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address ?: '';
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
