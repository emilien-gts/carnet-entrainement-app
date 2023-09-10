<?php

namespace App\Session\Service;

use App\Session\Entity\Session;

class SessionManager
{
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

    public function archive(Session $session, bool $toArchive): Session
    {
        $session->isArchived = $toArchive;
        if ($toArchive) {
            $session->isFavorite = false;
        }

        return $session;
    }
}
