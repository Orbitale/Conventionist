<?php

namespace App\Entity;

use App\Repository\AnimationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimationRepository::class)]
class Animation implements HasNestedRelations
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column]
    private int $maxNumberOfParticipants = 1;

    /** @var Collection<ScheduledAnimation> */
    #[ORM\OneToMany(targetEntity: ScheduledAnimation::class, mappedBy: 'animation')]
    #[Assert\Valid]
    private Collection $scheduledAnimations;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->scheduledAnimations = new ArrayCollection();
    }

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

    public function getMaxNumberOfParticipants(): ?int
    {
        return $this->maxNumberOfParticipants;
    }

    public function setMaxNumberOfParticipants(?int $maxNumberOfParticipants): void
    {
        $this->maxNumberOfParticipants = $maxNumberOfParticipants ?: 1;
    }

    public function getScheduledAnimations(): Collection
    {
        return $this->scheduledAnimations;
    }

    public function addScheduledAnimation(ScheduledAnimation $scheduledAnimation): void
    {
        if ($this->scheduledAnimations->contains($scheduledAnimation)) {
            return;
        }

        $this->scheduledAnimations->add($scheduledAnimation);
    }

    public function hasScheduledAnimation(ScheduledAnimation $scheduledAnimation): bool
    {
        return $this->scheduledAnimations->contains($scheduledAnimation);
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->scheduledAnimations as $animation) {
            $animation->setAnimation($this);
        }
    }
}
