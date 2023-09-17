<?php

namespace App\Program\Service;

use App\Program\Entity\Program;
use App\Program\Entity\ProgramVersion;
use App\Session\Entity\Session;

class ProgramManager
{
    public function createVersion(Program $program): ?ProgramVersion
    {
        $currentVersion = $program->getCurrentVersion();
        if ($currentVersion && $program->sameAs($currentVersion)) {
            return null;
        }

        $sessions = $program->getSessions()
            ->filter(fn (?Session $s) => null !== $s)
            ->map(fn (Session $s) => $s->toArray()) /* @phpstan-ignore-line */
            ->toArray();

        $v = new ProgramVersion($program);
        $v->version = $program->getCurrentVersionNumber() + 1;
        $v->data = $sessions;

        return $v;
    }

    public function duplicate(Program $program): Program
    {
        $p = clone $program;
        if ($p->name) {
            $p->name = \sprintf('Copie de %s', $program->name);
        }

        return $p;
    }
}
