<?php

namespace App\Core\Contracts;

interface FavoriteAwareInterface
{
    public function setIsFavorite(bool $isFavorite): self;

    public function toggleFavorite(): void;
}
