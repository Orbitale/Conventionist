<?php

namespace App\Validator;

use App\Entity\ScheduledAnimation;
use App\Repository\ScheduledAnimationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NoOverlappingSchedulesValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ScheduledAnimationRepository $scheduledAnimationRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NoOverlappingSchedules $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof ScheduledAnimation) {
            throw new \RuntimeException(\sprintf(
                'The "%s" validation constraint can only be used on the "%s" class.',
                NoOverlappingSchedules::class, ScheduledAnimation::class,
            ));
        }

        $hasSimilarSchedules = $this->scheduledAnimationRepository->hasSimilar($value);
        if ($hasSimilarSchedules) {
            $this->context->buildViolation($constraint->sameAnimationAlreadyProposed)
                ->atPath('animation')
                ->addViolation()
            ;
            $this->context->buildViolation('')->atPath('timeSlot')->addViolation();
        }
    }
}
