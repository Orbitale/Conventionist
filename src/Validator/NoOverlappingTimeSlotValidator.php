<?php

namespace App\Validator;

use App\Entity\TimeSlot;
use App\Repository\TimeSlotRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NoOverlappingTimeSlotValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TimeSlotRepository $timeSlotRepository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NoOverlappingTimeSlot $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof TimeSlot) {
            throw new \RuntimeException(\sprintf(
                'The "%s" validation constraint can only be used on the "%s" class.',
                NoOverlappingTimeSlot::class, TimeSlot::class,
            ));
        }

        $hasOverlap = $this->timeSlotRepository->hasOverlap($value);
        if ($hasOverlap) {
            $this->context->buildViolation($constraint->timeSlotAlreadyTaken)
                ->atPath('startsAt')
                ->addViolation()
            ;
            $this->context->buildViolation('')->atPath('endsAt')->addViolation();
        }
    }
}
