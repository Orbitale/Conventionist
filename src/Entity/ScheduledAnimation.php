<?php

namespace App\Entity;

use App\Enum\ScheduleAnimationState;
use App\Repository\ScheduledAnimationRepository;
use App\Validator\NoOverlappingSchedules;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduledAnimationRepository::class)]
#[NoOverlappingSchedules]
class ScheduledAnimation
{
    use Field\Id;

    #[ORM\Column(type: 'string', length: 255, enumType: ScheduleAnimationState::class)]
    #[Assert\NotBlank]
    private ScheduleAnimationState $state = ScheduleAnimationState::CREATED;

    #[ORM\ManyToOne(inversedBy: 'scheduledAnimations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Animation $animation = null;

    #[ORM\ManyToOne(inversedBy: 'scheduledAnimations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private TimeSlot $timeSlot;

    public function __toString(): string
    {
        return sprintf("%s (⏲ %s ➡ %s)", $this->animation, $this->timeSlot?->getStartsAt()->format('Y-m-d H:i:s'), $this->timeSlot?->getEndsAt()->format('Y-m-d H:i:s'));
    }

    public function accept(): void
    {
        if (!$this->canChangeState()) {
            return;
        }

        $this->state = ScheduleAnimationState::ACCEPTED;
    }

    public function reject(): void
    {
        if (!$this->canChangeState()) {
            return;
        }

        $this->state = ScheduleAnimationState::REJECTED;
    }

    public function stateCssClass(): string
    {
        return $this->state->getCssClass();
    }

    public function getStateColor(): string
    {
        return $this->state->getColor();
    }

    public function getStateString(): string
    {
        return $this->state->value;
    }

    public function isPendingReview(): bool
    {
        return $this->state === ScheduleAnimationState::PENDING_REVIEW;
    }

    public function isAccepted(): bool
    {
        return $this->state === ScheduleAnimationState::ACCEPTED;
    }

    public function canChangeState(): bool
    {
        return $this->state === ScheduleAnimationState::CREATED
            || $this->state === ScheduleAnimationState::PENDING_REVIEW;
    }

    public function getEvent(): Event
    {
        return $this->timeSlot->getEvent();
    }

    //

    public function getState(): ScheduleAnimationState
    {
        return $this->state;
    }

    public function setState(ScheduleAnimationState $state): void
    {
        $this->state = $state;
    }

    public function getAnimation(): ?Animation
    {
        return $this->animation;
    }

    public function setAnimation(Animation $animation): void
    {
        $this->animation = $animation;
    }

    public function getTimeSlot(): TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(TimeSlot $timeSlot): void
    {
        $this->timeSlot = $timeSlot;
    }
}
