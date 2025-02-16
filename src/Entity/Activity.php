<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activities')]
class Activity implements HasNestedRelations, HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(name: 'max_number_of_participants', type: Types::INTEGER, nullable: true)]
    private ?int $maxNumberOfParticipants = null;

    #[ORM\Column(name: 'needed_equipment', type: Types::JSON, nullable: false)]
    private array $neededEquipment = [];

    /** @var Collection<ScheduledActivity> */
    #[ORM\OneToMany(targetEntity: ScheduledActivity::class, mappedBy: 'activity')]
    #[Assert\Valid]
    private Collection $scheduledActivities;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->scheduledActivities = new ArrayCollection();
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
        $this->maxNumberOfParticipants = $maxNumberOfParticipants;
    }

    public function getScheduledActivities(): Collection
    {
        return $this->scheduledActivities;
    }

    public function addScheduledActivity(ScheduledActivity $scheduledActivity): void
    {
        if ($this->scheduledActivities->contains($scheduledActivity)) {
            return;
        }

        $this->scheduledActivities->add($scheduledActivity);
    }

    public function hasScheduledActivity(ScheduledActivity $scheduledActivity): bool
    {
        return $this->scheduledActivities->contains($scheduledActivity);
    }

    public function refreshNestedRelations(): void
    {
        foreach ($this->scheduledActivities as $activity) {
            $activity->setActivity($this);
        }
    }

    public function getNeededEquipment(): array
    {
        return $this->neededEquipment;
    }

    public function setNeededEquipment(array $neededEquipment): void
    {
        $this->neededEquipment = $neededEquipment;
    }
}
