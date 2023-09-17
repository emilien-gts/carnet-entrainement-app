<?php

namespace App\Training\Entity;

use App\Core\Trait\IdTrait;
use App\Program\Entity\Program;
use App\Session\Entity\Session;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Training
{
    use IdTrait;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTime $date = null;

    #[ORM\ManyToOne(targetEntity: Program::class)]
    public ?Program $program = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $session = null;

    /**
     * @var ArrayCollection<int, TrainingExercise>
     */
    #[ORM\OneToMany(mappedBy: 'training', targetEntity: TrainingExercise::class, cascade: ['ALL'])]
    public Collection $exercises;

    public function __construct()
    {
        $this->exercises = new ArrayCollection();
    }

    public function addExercise(TrainingExercise $exercise): void
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises->add($exercise);
            $exercise->training = $this;
        }
    }

    public function removeExercise(TrainingExercise $exercise): void
    {
        if ($this->exercises->contains($exercise)) {
            $this->exercises->removeElement($exercise);
            $exercise->training = null;
        }
    }

    public function basedOnSession(): bool
    {
        return null !== $this->session && null === $this->program;
    }

    public function __toString(): string
    {
        return (string) $this->session;
    }
}
