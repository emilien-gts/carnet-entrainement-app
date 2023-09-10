<?php

namespace App\Session\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

#[\Attribute()]
class TempoConstraint extends Regex
{
    /** @var string */
    public $pattern = '/^[0-9]{4}$/';

    public function __construct()
    {
        parent::__construct($this->getOptions());
    }

    protected function getOptions(array $options = []): array
    {
        return array_merge([
            'message' => 'validation.incorrect_tempo',
            'pattern' => $this->pattern,
        ], $options);
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}
