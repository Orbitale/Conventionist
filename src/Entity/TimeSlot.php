<?php

namespace App\Entity;

use App\Repository\TimeSlotRepository;
use App\Validator\NoOverlappingTimeSlot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeSlotRepository::class)]
#[NoOverlappingTimeSlot]
class TimeSlot
{
    use Field\Id { __construct as generateId; }
    use Field\StartEndDates;

    #[ORM\ManyToOne(targetEntity: TimeSlotCategory::class)]
    #[ORM\JoinColumn(name: 'category_id', nullable: true)]
    private ?TimeSlotCategory $category = null;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'event_id', nullable: false)]
    private Event $event;

    #[ORM\ManyToOne(targetEntity: Table::class, inversedBy: 'timeSlots')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private Table $table;

    #[ORM\OneToMany(targetEntity: ScheduledAnimation::class, mappedBy: 'timeSlot')]
    private Collection $scheduledAnimations;

    public function __construct()
    {
        $this->generateId();
        $this->scheduledAnimations = new ArrayCollection();
    }

    public static function create(Event $event, Table $table, \DateTimeImmutable $startsAt, \DateTimeImmutable $endsAt): self
    {
        $item = new self();

        $item->startsAt = $startsAt;
        $item->endsAt = $endsAt;
        $item->event = $event;
        $item->table = $table;

        return $item;
    }

    public function __toString(): string
    {
        return sprintf("%s (⏲ %s ➡ %s)", $this->table, $this->startsAt?->format('Y-m-d H:i:s'), $this->endsAt?->format('Y-m-d H:i:s'));
    }

    #[Assert\IsTrue(message: 'Time slot start and end date must be included in start and end date from the associated Event.')]
    public function isEventDateValid(): bool
    {
        return $this->startsAt >= $this->event->getStartsAt()
            && $this->startsAt <= $this->event->getEndsAt()
            && $this->endsAt >= $this->event->getStartsAt()
            && $this->endsAt <= $this->event->getEndsAt()
        ;
    }

    public function isInHour(int $hour): bool
    {
        return $this->getStartsAt()->format('H') <= $hour
            && $this->getEndsAt()->format('H') > $hour;
    }

    //

    public function getCategory(): ?TimeSlotCategory
    {
        return $this->category;
    }

    public function setCategory(?TimeSlotCategory $category): void
    {
        $this->category = $category;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    /**
     * @return Collection<ScheduledAnimation>
     */
    public function getScheduledAnimations(): Collection
    {
        return $this->scheduledAnimations;
    }
}
