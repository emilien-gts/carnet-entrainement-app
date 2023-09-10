<?php

namespace App\Core\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait FavoriteTrait
{
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    public bool $isFavorite = false;

    public function setIsFavorite(bool $isFavorite): self
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    public function toggleFavorite(): void
    {
        $this->isFavorite = !$this->isFavorite;
    }
}
