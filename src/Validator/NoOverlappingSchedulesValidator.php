<?php

namespace App\Validator;

use App\Entity\ScheduledActivity;
use App\Repository\ScheduledActivityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NoOverlappingSchedulesValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ScheduledActivityRepository $scheduledActivityRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NoOverlappingSchedules $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof ScheduledActivity) {
            throw new \RuntimeException(\sprintf('The "%s" validation constraint can only be used on the "%s" class.', NoOverlappingSchedules::class, ScheduledActivity::class));
        }

        $hasSimilarSchedules = $this->scheduledActivityRepository->hasSimilar($value);
        if ($hasSimilarSchedules) {
            $this->context->buildViolation($constraint->sameActivityAlreadyProposed)
                ->atPath('activity')
                ->addViolation()
            ;
            $this->context->buildViolation('')->atPath('timeSlot')->addViolation();
        }
    }
}
