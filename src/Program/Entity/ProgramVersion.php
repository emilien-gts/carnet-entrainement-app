<?php

namespace App\Program\Entity;

use App\Core\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProgramVersion
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Program::class, inversedBy: 'versions')]
    public Program $program;

    #[ORM\Column(type: Types::SMALLINT)]
    public ?int $version = null;

    /**
     * @var array<int, int[]>
     */
    #[ORM\Column(type: Types::JSON)]
    public array $data = [];

    public function __construct(Program $program)
    {
        $this->program = $program;
    }
}
