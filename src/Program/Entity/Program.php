<?php

namespace App\Program\Entity;

use App\Core\Contracts\ArchiveAwareInterface;
use App\Core\Contracts\FavoriteAwareInterface;
use App\Core\Contracts\Versioned;
use App\Core\Trait\ArchiveTrait;
use App\Core\Trait\FavoriteTrait;
use App\Core\Trait\IdTrait;
use App\Core\Utils;
use App\Program\Repository\ProgramRepository;
use App\Session\Entity\Session;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program implements FavoriteAwareInterface, ArchiveAwareInterface, Versioned
{
    use IdTrait;
    use FavoriteTrait;
    use ArchiveTrait;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    // Sessions

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $monday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $tuesday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $wednesday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $thursday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $friday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $saturday = null;

    #[ORM\ManyToOne(targetEntity: Session::class)]
    public ?Session $sunday = null;

    /**
     * @var ArrayCollection<int, ProgramVersion>
     */
    #[ORM\OneToMany(mappedBy: 'program', targetEntity: ProgramVersion::class, cascade: ['ALL'])]
    public Collection $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getUniqueName(): string
    {
        $s = \sprintf('Programme %03d', $this->id);
        if ($this->name) {
            $s .= \sprintf(' - %s', $this->name);
        }

        return $s;
    }

    /**
     * @return ArrayCollection<int, Session|null>
     */
    public function getSessions(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (Utils::day_of_weeks() as $dayOfWeek) {
            $collection->add($this->$dayOfWeek);
        }

        return $collection;
    }

    /**
     * @param ProgramVersion $version
     */
    public function sameAs($version): bool
    {
        $sessions = $this->getSessions()->map(fn (Session $s) => $s->id)->toArray(); /* @phpstan-ignore-line */

        return Utils::are_arrays_equal($sessions, $version->data);
    }

    public function getCurrentVersion(): ?ProgramVersion
    {
        return $this->versions->last() ?: null;
    }

    public function getCurrentVersionNumber(): int
    {
        return $this->versions->count() ?: 0;
    }

    public function __clone(): void
    {
        $this->id = null;
    }

    public function __toString(): string
    {
        return $this->getUniqueName();
    }
}
