<?php

namespace App\Session\Entity;

use App\Core\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SessionVersion
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'versions')]
    public Session $session;

    #[ORM\Column(type: Types::SMALLINT)]
    public ?int $version = null;

    /**
     * @var array<int, array<string, string|int>>
     */
    #[ORM\Column(type: Types::JSON)]
    public array $data = [];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
}
