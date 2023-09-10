<?php

namespace App\Core\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ArchiveTrait
{
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    public bool $isArchived = false;

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }
}
