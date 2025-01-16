<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class NoOverlappingTimeSlot extends Constraint
{
    public string $timeSlotAlreadyTaken = 'This time slot overlaps with another at the same time and place.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return NoOverlappingTimeSlotValidator::class;
    }
}
