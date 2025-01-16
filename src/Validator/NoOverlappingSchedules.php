<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class NoOverlappingSchedules extends Constraint
{
    public string $sameAnimationAlreadyProposed = 'This schedule overlaps with another animation at the same time and place.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return NoOverlappingSchedulesValidator::class;
    }
}
