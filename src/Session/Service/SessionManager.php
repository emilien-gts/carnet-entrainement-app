<?php

namespace App\Session\Service;

use App\Session\Entity\Session;
use App\Session\Entity\SessionExercise;
use App\Session\Entity\SessionVersion;

class SessionManager
{
    public function createVersion(Session $session): ?SessionVersion
    {
        $currentVersion = $session->getCurrentVersion();
        if ($currentVersion && $session->sameExercisesAs($currentVersion)) {
            return null;
        }

        $exercices = $session->exercises->map(fn (SessionExercise $se) => $se->toArray())->toArray();

        $v = new SessionVersion($session);
        $v->version = $session->getCurrentVersionNumber() + 1;
        $v->data = $exercices;

        return $v;
    }

    public function duplicate(Session $session): Session
    {
        $s = clone $session;
        if ($s->name) {
            $s->name = \sprintf('Copie de %s', $session->name);
        }

        $exercises = $session->exercises;
        foreach ($exercises as $exercise) {
            $e = clone $exercise;
            $s->addExercise($e);
        }

        return $s;
    }
}
