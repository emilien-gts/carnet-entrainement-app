<?php

namespace App\Session\Validator\Constraint;

use App\Session\Validator\ExerciseValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute()]
class ExerciseConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return ExerciseValidator::class;
    }

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
