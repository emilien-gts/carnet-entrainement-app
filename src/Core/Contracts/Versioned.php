<?php

namespace App\Core\Contracts;

interface Versioned
{
    public function sameAs(mixed $version): bool;

    public function getCurrentVersion(): ?object;

    public function getCurrentVersionNumber(): int;
}
