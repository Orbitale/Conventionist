<?php

namespace App\Entity;

use App\Enum\ScheduleActivityState;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;
    use Field\GenericContact;
    use Field\StartEndDates;
    use Field\Published;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    private ?string $name = '';

    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, nullable: false)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = '';

    #[ORM\Column(name: 'is_online_event', type: Types::BOOLEAN, nullable: false)]
    #[Assert\Type('bool')]
    #[Assert\NotNull()]
    private bool $isOnlineEvent = false;

    #[ORM\Column(name: 'allow_activity_registration', type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    #[Assert\Type('bool')]
    #[Assert\NotNull()]
    private bool $allowActivityRegistration = true;

    #[ORM\Column(name: 'allow_attendee_registration', type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    #[Assert\Type('bool')]
    #[Assert\NotNull()]
    private bool $allowAttendeeRegistration = true;

    #[ORM\Column(name: "locale", type: "string", nullable: true)]
    #[Assert\Locale]
    private ?string $locale = null;

    #[ORM\Column(name: "url", type: "string", nullable: true)]
    private ?string $url = null;

    /**
     * @Assert\Image(
     *     mimeTypes={"image/jpeg", "image/png"},
     *     minWidth=1000,
     *     minRatio="1.3",
     *     allowPortrait=false,
     *     allowSquare=false,
     *     detectCorrupted=true
     * )
     */
    private ?UploadedFile $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private Venue $venue;

    /** @var Collection<TimeSlot> */
    #[ORM\OneToMany(targetEntity: TimeSlot::class, mappedBy: 'event')]
    private Collection $timeSlots;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->timeSlots = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function getDays(): array
    {
        $days = [
            $this->startsAt->format('Y-m-d') => $this->startsAt,
        ];

        for ($i = 1; $i <= $this->startsAt->diff($this->endsAt)->days; $i++) {
            $date = $this->startsAt->modify("+{$i} days");
            $days[$date->format('Y-m-d')] = $date;
        }

        return $days;
    }

    /**
     * @return array<ScheduledActivity>
     */
    public function getDaySlotsForPublicView(\DateTimeInterface $day): array
    {
        $slotsAtDay = \array_filter($this->timeSlots->toArray(), static fn (TimeSlot $slot): bool => $slot->canBeShownToPublic($day));

        \usort($slotsAtDay, static fn (TimeSlot $a, TimeSlot $b) => $a->getStartsAt() <=> $b->getStartsAt());

        return $slotsAtDay;
    }

    public function getScheduledActivityById(string $id): ScheduledActivity
    {
        foreach ($this->timeSlots as $slot) {
            if ($activity = $slot->findScheduledActivityById($id)) {
                return $activity;
            }
        }

        throw new \RuntimeException(\sprintf('Could not find scheduled activity with id "%s" in Event "%s".', $id, $this->name));
    }

    /**
     * @return Collection<TimeSlot>
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function getCalendarResourceJson(): array
    {
        $json = [];

        foreach ($this->venue->getFloors() as $floor) {
            $json[] = $floor->getCalendarResourceJson();
        }

        return $json;
    }

    /**
     * @param array<ScheduleActivityState> $states
     */
    public function getCalendarSchedulesJson(array $states): array
    {
        $json = [];

        foreach ($this->getTimeSlots() as $slot) {
            $activities = $slot->getScheduledActivities();
            foreach ($activities as $scheduledActivity) {
                if (!\in_array($scheduledActivity->getState(), $states)) {
                    continue;
                }
                $json[] = [
                    'id' => $scheduledActivity->getId(),
                    'title' => $scheduledActivity->getActivity()?->getName(),
                    'start' => $slot->getStartsAt(),
                    'end' => $slot->getEndsAt(),
                    'resourceId' => $slot->getBooth()->getId(),
                    'extendedProps' => [
                        'type' => 'activity',
                        'description' => $scheduledActivity->getActivity()?->getDescription(),
                    ],
                    'color' => $scheduledActivity->getStateColor(),
                ];
            }
            if (!$activities->count()) {
                $json[] = [
                    'id' => $slot->getId(),
                    'title' => '',
                    'start' => $slot->getStartsAt(),
                    'end' => $slot->getEndsAt(),
                    'resourceId' => $slot->getBooth()->getId(),
                    'extendedProps' => ['type' => 'empty_slot'],
                    'color' => '#000',
                ];
            }
        }

        return $json;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?: '';
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function isOnlineEvent(): bool
    {
        return $this->isOnlineEvent;
    }

    public function setIsOnlineEvent(bool $isOnlineEvent): void
    {
        $this->isOnlineEvent = $isOnlineEvent;
    }

    public function getAllowActivityRegistration(): bool
    {
        return $this->allowActivityRegistration;
    }

    public function setAllowActivityRegistration(bool $allowActivityRegistration): void
    {
        $this->allowActivityRegistration = $allowActivityRegistration;
    }

    public function getAllowAttendeeRegistration(): bool
    {
        return $this->allowAttendeeRegistration;
    }

    public function setAllowAttendeeRegistration(bool $allowAttendeeRegistration): void
    {
        $this->allowAttendeeRegistration = $allowAttendeeRegistration;
    }

    public function getVenue(): Venue
    {
        return $this->venue;
    }

    public function setVenue(Venue $venue): void
    {
        $this->venue = $venue;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function addTimeslot(TimeSlot $slot): void
    {
        if (!$this->timeSlots->contains($slot)) {
            $this->timeSlots->add($slot);
        }
    }
}
