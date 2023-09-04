<?php

namespace App\Session\Validator;

use App\Session\Entity\Exercise;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExerciseValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof Exercise) {
            throw new UnexpectedValueException($value, Exercise::class);
        }

        if ($value->mainMuscle && !empty($value->secondaryMuscles)) {
            $this->validateMuscle($value);
        }
    }

    private function validateMuscle(Exercise $exercise): void
    {
        if (\in_array($exercise->mainMuscle, $exercise->secondaryMuscles)) {
            $this->context->buildViolation('Impossible d\'ajouter le muscle principal dans la liste des muscles secondaires')
                ->atPath('secondaryMuscles')
                ->addViolation();
        }
    }
}
