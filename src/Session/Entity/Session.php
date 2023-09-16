<?php

namespace App\Session\Entity;

use App\Core\Contracts\ArchiveAwareInterface;
use App\Core\Contracts\FavoriteAwareInterface;
use App\Core\Contracts\Versioned;
use App\Core\Trait\ArchiveTrait;
use App\Core\Trait\FavoriteTrait;
use App\Core\Trait\IdTrait;
use App\Core\Trait\TimestampableTrait;
use App\Core\Utils;
use App\Session\Validator\Constraint\SessionConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[SessionConstraint]
#[ORM\HasLifecycleCallbacks]
class Session implements FavoriteAwareInterface, ArchiveAwareInterface, Versioned
{
    use IdTrait;
    use FavoriteTrait;
    use ArchiveTrait;
    use TimestampableTrait;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    /**
     * @var ArrayCollection<int, SessionExercise>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionExercise::class, cascade: ['ALL'])]
    #[Assert\Valid]
    public Collection $exercises;

    /**
     * @var ArrayCollection<int, SessionVersion>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionVersion::class, cascade: ['ALL'])]
    public Collection $versions;

    public function __construct()
    {
        $this->exercises = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }

    public function getUniqueName(): string
    {
        $s = \sprintf('Séance %03d', $this->id);
        if ($this->name) {
            $s .= \sprintf(' - %s', $this->name);
        }

        return $s;
    }

    public function addExercise(SessionExercise $exercise): void
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises->add($exercise);
            $exercise->session = $this;
        }
    }

    public function removeExercise(SessionExercise $exercise): void
    {
        if ($this->exercises->contains($exercise)) {
            $this->exercises->removeElement($exercise);
            $exercise->session = null;
        }
    }

    /**
     * @param SessionVersion $version
     */
    public function sameAs($version): bool
    {
        $exercices = $this->exercises->map(fn (SessionExercise $se) => $se->toArray())->toArray();

        return Utils::are_arrays_equal($exercices, $version->data);
    }

    public function getCurrentVersion(): ?SessionVersion
    {
        return $this->versions->last() ?: null;
    }

    public function getCurrentVersionNumber(): int
    {
        return $this->versions->count() ?: 0;
    }

    public function toArray(): array
    {
        return ['id' => $this->id ?? '', 'version' => $this->getCurrentVersionNumber()];
    }

    public function __clone(): void
    {
        $this->id = null;
        $this->exercises = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getUniqueName();
    }
}
