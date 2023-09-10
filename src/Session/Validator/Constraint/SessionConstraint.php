<?php

namespace App\Session\Validator\Constraint;

use App\Session\Validator\SessionValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute()]
class SessionConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return SessionValidator::class;
    }

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
