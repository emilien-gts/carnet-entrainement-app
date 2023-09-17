<?php

namespace App\Training\Entity;

use App\Core\Trait\IdTrait;
use App\Session\Entity\Exercise;
use App\Session\Validator\Constraint\TempoConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TrainingExercise
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Training::class, inversedBy: 'exercises')]
    public ?Training $training = null;

    #[ORM\ManyToOne(targetEntity: Exercise::class)]
    public ?Exercise $exercise = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[TempoConstraint]
    public ?int $tempo = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive]
    public ?int $rest = null; // seconds

    #[ORm\Column(type: Types::STRING, nullable: true)]
    public ?string $description = null;

    /**
     * @var ArrayCollection<int, TrainingExerciseSet>
     */
    #[ORM\OneToMany(mappedBy: 'trainingExercise', targetEntity: TrainingExerciseSet::class, cascade: ['ALL'])]
    #[ORM\OrderBy(['setNumber' => 'ASC'])]
    public Collection $sets;

    public function __construct()
    {
        $this->sets = new ArrayCollection();
    }

    public function addSet(TrainingExerciseSet $set): void
    {
        if (!$this->sets->contains($set)) {
            $this->sets->add($set);
            $set->trainingExercise = $this;
        }
    }

    public function removeSet(TrainingExerciseSet $set): void
    {
        if ($this->sets->contains($set)) {
            $this->sets->removeElement($set);
            $set->trainingExercise = null;
        }
    }

    public function __toString(): string
    {
        return \sprintf('%s (%s)', $this->exercise, $this->training);
    }
}
