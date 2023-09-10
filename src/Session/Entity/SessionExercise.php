<?php

namespace App\Session\Entity;

use App\Core\Trait\IdTrait;
use App\Session\Validator\Constraint\TempoConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class SessionExercise
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'exercises')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    public ?Session $session = null;

    #[ORM\ManyToOne(targetEntity: Exercise::class)]
    public ?Exercise $exercise = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $nbSet = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $nbReps = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[TempoConstraint]
    public ?int $tempo = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $rest = null; // seconds

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $number = null;

    #[ORm\Column(type: Types::STRING, nullable: true)]
    public ?string $description = null;

    public function __clone(): void
    {
        $this->id = null;
    }

    public function __toString(): string
    {
        return (string) $this->exercise;
    }
}
