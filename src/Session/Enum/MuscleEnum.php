<?php

namespace App\Session\Enum;

use App\Core\Contracts\Enum\Labelized;

enum MuscleEnum: string implements Labelized
{
    case TRAPEZIUS = 'trapezius';
    case DELTOIDS = 'deltoids';
    case CHEST = 'chest';
    case BICEPS = 'biceps';
    case FOREARMS = 'forearms';
    case ADDUCTORS = 'adductors';
    case ABDUCTORS = 'abductors';
    case QUADRICEPS = 'quadriceps';
    case BACK = 'back';
    case TRICEPS = 'triceps';
    case GLUTES = 'glutes';
    case HAMSTRINGS = 'hamstrings';
    case CALVES = 'calves';
    case ABS = 'abs';

    public function label(): string
    {
        return match ($this) {
            self::TRAPEZIUS => 'Trapèzes',
            self::DELTOIDS => 'Deltoïdes',
            self::CHEST => 'Pectoraux',
            self::BICEPS => 'Biceps',
            self::FOREARMS => 'Avant-bras',
            self::ADDUCTORS => 'Adducteurs',
            self::ABDUCTORS => 'Abducteurs',
            self::QUADRICEPS => 'Quadriceps',
            self::BACK => 'Dorsaux',
            self::TRICEPS => 'Triceps',
            self::GLUTES => 'Fessiers',
            self::HAMSTRINGS => 'Ischios',
            self::CALVES => 'Mollets',
            self::ABS => 'Abdominaux'
        };
    }

    public static function filterChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case->value;
        }

        return $choices;
    }
}
