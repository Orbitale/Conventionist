<?php

namespace App\Entity;

use App\Enum\ScheduleActivityState;
use App\Repository\ScheduledActivityRepository;
use App\Validator\NoOverlappingSchedules;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ScheduledActivityRepository::class)]
#[ORM\Table(name: 'scheduled_activities')]
#[NoOverlappingSchedules]
class ScheduledActivity
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(type: 'string', length: 255, enumType: ScheduleActivityState::class)]
    #[Assert\NotBlank]
    private ScheduleActivityState $state = ScheduleActivityState::CREATED;

    #[ORM\ManyToOne(targetEntity: Activity::class, cascade: ['persist'], inversedBy: 'scheduledActivities')]
    #[ORM\JoinColumn(name: 'activity_id', nullable: false)]
    #[Assert\NotBlank]
    private ?Activity $activity = null;

    #[ORM\ManyToOne(targetEntity: TimeSlot::class, inversedBy: 'scheduledActivities')]
    #[ORM\JoinColumn(name: 'time_slot_id', nullable: false)]
    #[Assert\NotBlank]
    private TimeSlot $timeSlot;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'submitted_by', referencedColumnName: 'id', nullable: true)]
    private ?User $submittedBy;

    // Used by form
    #[Assert\Type(Activity::class)]
    public ?Activity $selectedActivity = null;

    // Used by form
    #[Assert\Type(Activity::class)]
    public ?Activity $newActivity = null;

    // Used by form
    #[Assert\Email(mode: Assert\Email::VALIDATION_MODE_STRICT, groups: ['submit_activity'])]
    #[Assert\NotBlank(groups: ['submit_activity'])]
    public ?string $email;

    #[ORM\Column(name: 'capacity', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $capacity = null;

    #[ORM\Column(name: 'waitlist_enabled', type: Types::BOOLEAN, nullable: false, options: ['default' => 0])]
    private bool $waitlistEnabled = false;

    #[ORM\Column(name: 'gm_checked_in_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $gmCheckedInAt = null;

    #[ORM\Column(name: 'grace_deadline_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $graceDeadlineAt = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id', nullable: true)]
    private ?Game $game = null;

    /** @var Collection<int, SafetyTool> */
    #[ORM\ManyToMany(targetEntity: SafetyTool::class)]
    #[ORM\JoinTable(name: 'scheduled_activity_safety_tool')]
    #[ORM\JoinColumn(name: 'scheduled_activity_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'safety_tool_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $safetyTools;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
        $this->safetyTools = new ArrayCollection();
    }

    public function __toString(): string
    {
        return \sprintf('%s (⏲ %s ➡ %s)', $this->activity, $this->timeSlot?->getStartsAt()->format('Y-m-d H:i:s'), $this->timeSlot?->getEndsAt()->format('Y-m-d H:i:s'));
    }

    #[Assert\Callback(groups: ['submit_activity'])]
    public function hasFormActivity(ExecutionContextInterface $context): void
    {
        if (!$this->newActivity && !$this->selectedActivity) {
            $context->addViolation('scheduled_activity');
        }
    }

    public function canBeRegisteredTo(): bool
    {
        return $this->isAccepted() && $this->getEvent()->getAllowAttendeeRegistration() && $this->timeSlot->getBooth()->getAllowAttendeeRegistration();
    }

    public function accept(): void
    {
        if (!$this->canChangeState()) {
            return;
        }

        $this->state = ScheduleActivityState::ACCEPTED;
    }

    public function reject(): void
    {
        if (!$this->canChangeState()) {
            return;
        }

        $this->state = ScheduleActivityState::REJECTED;
    }

    public function stateCssClass(): string
    {
        return $this->state->getCssClass();
    }

    public function getStateColor(): string
    {
        return $this->state->getColor();
    }

    public function isCreated(): bool
    {
        return $this->state === ScheduleActivityState::CREATED;
    }

    public function isPendingReview(): bool
    {
        return $this->state === ScheduleActivityState::PENDING_REVIEW;
    }

    public function isAccepted(): bool
    {
        return $this->state === ScheduleActivityState::ACCEPTED;
    }

    public function canChangeState(): bool
    {
        return $this->state === ScheduleActivityState::CREATED
            || $this->state === ScheduleActivityState::PENDING_REVIEW;
    }

    public function getEvent(): Event
    {
        return $this->timeSlot->getEvent();
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->timeSlot->getStartsAt();
    }

    public function getEndsAt(): \DateTimeImmutable
    {
        return $this->timeSlot->getEndsAt();
    }

    public function getState(): ScheduleActivityState
    {
        return $this->state;
    }

    public function setState(ScheduleActivityState $state): void
    {
        $this->state = $state;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
    }

    public function getTimeSlot(): TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(TimeSlot $timeSlot): void
    {
        $this->timeSlot = $timeSlot;
        $timeSlot->addScheduledActivity($this);
    }

    public function getSubmittedBy(): ?User
    {
        return $this->submittedBy;
    }

    public function setSubmittedBy(User $user): void
    {
        $this->submittedBy = $user;
        $this->email = $user->getEmail();

        if ($user->isEmailConfirmed() && $this->isCreated()) {
            $this->state = ScheduleActivityState::PENDING_REVIEW;
        }
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function isWaitlistEnabled(): bool
    {
        return $this->waitlistEnabled;
    }

    public function setWaitlistEnabled(bool $waitlistEnabled): void
    {
        $this->waitlistEnabled = $waitlistEnabled;
    }

    public function getGmCheckedInAt(): ?\DateTimeImmutable
    {
        return $this->gmCheckedInAt;
    }

    public function setGmCheckedInAt(?\DateTimeImmutable $gmCheckedInAt): void
    {
        $this->gmCheckedInAt = $gmCheckedInAt;
    }

    public function getGraceDeadlineAt(): ?\DateTimeImmutable
    {
        return $this->graceDeadlineAt;
    }

    public function setGraceDeadlineAt(?\DateTimeImmutable $graceDeadlineAt): void
    {
        $this->graceDeadlineAt = $graceDeadlineAt;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): void
    {
        $this->game = $game;
    }

    /**
     * @return Collection<int, SafetyTool>
     */
    public function getSafetyTools(): Collection
    {
        return $this->safetyTools;
    }

    public function addSafetyTool(SafetyTool $tool): void
    {
        if (!$this->safetyTools->contains($tool)) {
            $this->safetyTools->add($tool);
        }
    }

    public function removeSafetyTool(SafetyTool $tool): void
    {
        $this->safetyTools->removeElement($tool);
    }

    public function hasGmCheckedIn(): bool
    {
        return null !== $this->gmCheckedInAt;
    }

    public function isPastGraceDeadline(?\DateTimeImmutable $at = null): bool
    {
        if (null === $this->graceDeadlineAt) {
            return false;
        }

        $at = $at ?? new \DateTimeImmutable();

        return $at > $this->graceDeadlineAt;
    }
}
