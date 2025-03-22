<?php

namespace App\Entity;

use App\Repository\TimeSlotRepository;
use App\Validator\NoOverlappingTimeSlot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeSlotRepository::class)]
#[NoOverlappingTimeSlot]
class TimeSlot implements HasCreators
{
    use Field\Id { __construct as generateId; }
    use Field\StartEndDates;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'timeSlots')]
    #[ORM\JoinColumn(name: 'event_id', nullable: false)]
    private Event $event;

    #[ORM\ManyToOne(targetEntity: Booth::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private Booth $booth;

    #[ORM\Column(name: 'is_open', type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    #[Assert\Type('bool')]
    #[Assert\NotNull]
    private bool $open = true;

    #[ORM\Column(name: 'available_equipment', type: Types::JSON, nullable: false, options: ['default' => '[]'])]
    private array $availableEquipment = [];

    /** @var Collection<ScheduledActivity> */
    #[ORM\OneToMany(targetEntity: ScheduledActivity::class, mappedBy: 'timeSlot')]
    private Collection $scheduledActivities;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
        $this->scheduledActivities = new ArrayCollection();
    }

    public static function create(Event $event, Booth $booth, \DateTimeImmutable $startsAt, \DateTimeImmutable $endsAt): self
    {
        $item = new self();

        $item->startsAt = $startsAt;
        $item->endsAt = $endsAt;
        $item->event = $event;
        $item->booth = $booth;

        return $item;
    }

    public function __toString(): string
    {
        return sprintf('%s (⏲ %s ➡ %s)', $this->booth, $this->startsAt?->format('Y-m-d H:i:s'), $this->endsAt?->format('Y-m-d H:i:s'));
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

    public function addScheduledActivity(ScheduledActivity $param): void
    {
        if ($this->scheduledActivities->contains($param)) {
            return;
        }

        $this->scheduledActivities->add($param);
    }

    public function isDay(\DateTimeInterface $day): bool
    {
        return $this->startsAt->format('Y-m-d') === $day->format('Y-m-d');
    }

    public function hasAcceptedActivity(): bool
    {
        return \array_any($this->scheduledActivities->toArray(), static fn ($activity) => $activity->isAccepted());
    }

    public function getAcceptedActivity(): ScheduledActivity
    {
        foreach ($this->scheduledActivities as $activity) {
            if ($activity->isAccepted()) {
                return $activity;
            }
        }

        throw new \RuntimeException('No accepted activity available.');
    }

    public function canBeShownToPublic(\DateTimeInterface $day): bool
    {
        return $this->isDay($day) && ($this->hasAcceptedActivity() || ($this->isOpen() && $this->event->getAllowActivityRegistration()));
    }

    public function isClosedForPlanning(): bool
    {
        if (!$this->open) {
            return true;
        }

        return $this->hasAcceptedActivity();
    }

    public function findScheduledActivityById(string $id): ?ScheduledActivity
    {
        return \array_find(
            $this->scheduledActivities->toArray(),
            static fn (ScheduledActivity $item) => $item->getId() === $id,
        );
    }

    /**
     * @return Collection<ScheduledActivity>
     */
    public function getAcceptedScheduledActivities(): Collection
    {
        return $this->scheduledActivities->filter(static fn (ScheduledActivity $activity) => $activity->isAccepted());
    }

    public function getCreators(): Collection
    {
        return $this->event->getCreators();
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;
        $this->event->addTimeslot($this);
    }

    public function getBooth(): Booth
    {
        return $this->booth;
    }

    public function setBooth(Booth $booth): void
    {
        $this->booth = $booth;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): void
    {
        $this->open = $open;
    }

    public function getAvailableEquipment(): array
    {
        return $this->availableEquipment;
    }

    public function setAvailableEquipment(array $availableEquipment): void
    {
        $this->availableEquipment = $availableEquipment;
    }

    /**
     * @return Collection<ScheduledActivity>
     */
    public function getScheduledActivities(): Collection
    {
        return $this->scheduledActivities;
    }
}
