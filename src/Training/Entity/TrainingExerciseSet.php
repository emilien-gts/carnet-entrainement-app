<?php

namespace App\Training\Entity;

use App\Core\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TrainingExerciseSet
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: TrainingExercise::class, inversedBy: 'sets')]
    public ?TrainingExercise $trainingExercise = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $setNumber = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $nbReps = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $weight = null;
}
