<?php

namespace App\Program\Entity;

use App\Core\Contracts\ArchiveAwareInterface;
use App\Core\Contracts\FavoriteAwareInterface;
use App\Core\Trait\ArchiveTrait;
use App\Core\Trait\FavoriteTrait;
use App\Core\Trait\IdTrait;
use App\Session\Entity\Session;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Program implements FavoriteAwareInterface, ArchiveAwareInterface
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

    public function getUniqueName(): string
    {
        $s = \sprintf('Programme %03d', $this->id);
        if ($this->name) {
            $s .= \sprintf(' - %s', $this->name);
        }

        return $s;
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
