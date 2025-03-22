<?php

namespace App\Entity;

use App\Enum\ScheduleActivityState;
use App\Repository\ScheduledActivityRepository;
use App\Validator\NoOverlappingSchedules;
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

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }

    public function __toString(): string
    {
        return sprintf('%s (⏲ %s ➡ %s)', $this->activity, $this->timeSlot?->getStartsAt()->format('Y-m-d H:i:s'), $this->timeSlot?->getEndsAt()->format('Y-m-d H:i:s'));
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
        return $this->isAccepted() && $this->getEvent()->getAllowAttendeeRegistration();
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

    public function setSubmittedBy(?User $user): void
    {
        $this->submittedBy = $user;
        $this->email = $user?->getEmail();
    }
}
