<?php

namespace App\Session\Entity;

use App\Core\Trait\IdTrait;
use App\Session\Enum\MuscleEnum;
use App\Session\Validator\Constraint\ExerciseConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
#[ExerciseConstraint]
class Exercise
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: MuscleEnum::class)]
    public ?MuscleEnum $mainMuscle = null;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON)]
    public array $secondaryMuscles = [];

    /**
     * @return array<int, MuscleEnum>
     */
    public function getSecondaryMuscles(): array
    {
        return \array_map(fn (string $muscle) => MuscleEnum::from($muscle), $this->secondaryMuscles);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
