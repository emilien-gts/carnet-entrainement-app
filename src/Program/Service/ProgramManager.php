<?php

namespace App\Program\Service;

use App\Program\Entity\Program;

class ProgramManager
{
    public function duplicate(Program $program): Program
    {
        $p = clone $program;
        if ($p->name) {
            $p->name = \sprintf('Copie de %s', $program->name);
        }

        return $p;
    }
}
