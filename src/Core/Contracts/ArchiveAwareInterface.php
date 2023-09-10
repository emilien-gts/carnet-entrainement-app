<?php

namespace App\Core\Contracts;

interface ArchiveAwareInterface
{
    public function setIsArchived(bool $isArchived): self;
}
