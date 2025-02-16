<?php

namespace App\Entity;

use App\Enum\ScheduleActivityState;
use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;
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

    #[ORM\Column(name: 'address', type: Types::TEXT, nullable: false)]
    private string $address = '';

    #[ORM\Column(name: 'is_online_event', type: Types::BOOLEAN, nullable: false)]
    #[Assert\Type('bool')]
    #[Assert\NotNull()]
    private bool $isOnlineEvent = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private Venue $venue;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function getScheduledActivityById(string $id): ScheduledActivity
    {
        foreach ($this->getVenue()->getFloors() as $floor) {
            foreach ($floor->getRooms() as $room) {
                foreach ($room->getBooths() as $booth) {
                    foreach ($booth->getTimeSlots() as $slot) {
                        foreach ($slot->getScheduledActivities() as $scheduledActivity) {
                            if ($scheduledActivity->getId() === $id) {
                                return $scheduledActivity;
                            }
                        }
                    }
                }
            }
        }

        throw new \RuntimeException(\sprintf('Could not find schedule "%s" in Event "%s".', $id, $this->name));
    }

    public function getTimeSlots(): array
    {
        $slots = [];

        foreach ($this->venue->getFloors() as $floor) {
            foreach ($floor->getRooms() as $room) {
                foreach ($room->getTimeSlots() as $slot) {
                    $slots[$slot->getId()] = $slot;
                }
            }
        }

        return \array_values($slots);
    }

    public function getCalendarResourceJson(): array
    {
        $json = [];

        foreach ($this->getVenue()->getFloors() as $floor) {
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

        foreach ($this->getVenue()->getFloors() as $floor) {
            foreach ($floor->getRooms() as $room) {
                foreach ($room->getBooths() as $booth) {
                    foreach ($booth->getTimeSlots() as $slot) {
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
                                'resourceId' => $booth->getId(),
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
                                'resourceId' => $booth->getId(),
                                'extendedProps' => ['type' => 'empty_slot'],
                                'color' => '#000',
                            ];
                        }
                    }
                }
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address ?: '';
    }

    public function isOnlineEvent(): bool
    {
        return $this->isOnlineEvent;
    }

    public function setIsOnlineEvent(bool $isOnlineEvent): void
    {
        $this->isOnlineEvent = $isOnlineEvent;
    }

    public function getVenue(): Venue
    {
        return $this->venue;
    }

    public function setVenue(Venue $venue): void
    {
        $this->venue = $venue;
    }
}
