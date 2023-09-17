<?php

namespace App\Training\Service;

use App\Program\Entity\Program;
use App\Session\Entity\Session;
use App\Session\Entity\SessionExercise;
use App\Training\Entity\Training;
use App\Training\Entity\TrainingExercise;
use App\Training\Entity\TrainingExerciseSet;

class TrainingManager
{
    /**
     * @return Training[]
     */
    public function createFromProgram(Program $program, \DateTime $date): array
    {
        $trainings = [];
        foreach ($program->getSessions() as $session) {
            if (null === $session) {
                $date->modify('+1 day');
                continue;
            }

            $trainings[] = $this->createFromSession($session, clone $date);
            $date->modify('+1 day');
        }

        return $trainings;
    }

    public function createFromSession(Session $session, \DateTime $date): Training
    {
        $training = new Training();
        $training->session = $session;
        $training->date = $date;

        foreach ($session->exercises as $sessionExercise) {
            $e = $this->createTrainingExerciseFromSessionExercise($sessionExercise);
            $training->addExercise($e);
        }

        return $training;
    }

    private function createTrainingExerciseFromSessionExercise(SessionExercise $se): TrainingExercise
    {
        $e = new TrainingExercise();
        $e->exercise = $se->exercise;
        $e->tempo = $se->tempo;
        $e->rest = $se->rest;
        $e->description = $se->description;

        if (null !== $se->nbSet) {
            for ($i = 1; $i <= $se->nbSet; ++$i) {
                $tes = new TrainingExerciseSet();
                $tes->nbReps = $se->nbReps;
                $tes->setNumber = $i;

                $e->addSet($tes);
            }
        }

        return $e;
    }
}
