<?php

namespace App\Session\Validator;

use App\Session\Entity\Session;
use App\Session\Entity\SessionExercise;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SessionValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Session) {
            throw new UnexpectedValueException($value, Session::class);
        }

        if ($value->exercises->isEmpty()) {
            $this->context->buildViolation('validation.at_least_one_exercise')
                ->atPath('exercises')
                ->addViolation();
        }

        $this->validateComplexExercices($value);
    }

    private function validateComplexExercices(Session $session): void
    {
        $numbers = $session->exercises
            ->filter(fn (SessionExercise $exercise) => null !== $exercise->number)
            ->map(fn (SessionExercise $exercise) => $exercise->number)
            ->toArray();

        $countNumbers = array_count_values($numbers);

        foreach ($countNumbers as $number => $count) {
            if ($count >= 2) {
                $exercisesWithSameNumber = \array_filter($session->exercises->toArray(), fn (SessionExercise $exercise) => $exercise->number === $number);
                $descriptions = array_map(fn (SessionExercise $exercise) => $exercise->description, $exercisesWithSameNumber);

                if (empty(\array_filter($descriptions, fn (?string $description) => null !== $description))) {
                    $this->context->buildViolation('validation.one_of_exercises_need_description')
                        ->setParameter('{{number}}', $number)
                        ->addViolation();
                }
            }
        }
    }
}
