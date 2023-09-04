<?php

namespace App\Session\Service;

use App\Core\Contracts\AbstractSynchronizer;
use App\Session\Entity\Exercise;
use App\Session\Enum\MuscleEnum;
use pcrov\JsonReader\Exception;

class ExerciseSynchronizer extends AbstractSynchronizer
{
    public const FILENAME = 'exercices.json';

    /**
     * @throws Exception
     */
    public function sync(): void
    {
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncExercise($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function syncExercise(array $data): void
    {
        $e = new Exercise();
        $e->name = $data['nom'];
        $e->description = $data['description'];
        if (isset($data['muscle_principal'])) {
            $e->mainMuscle = MuscleEnum::tryFrom($data['muscle_principal']);
        }
        if (isset($data['muscles_secondaires'])) {
            $e->secondaryMuscles = $data['muscles_secondaires'];
        }

        $this->em->persist($e);
    }

    protected function getFilename(): string
    {
        return self::FILENAME;
    }

    protected function getClassname(): ?string
    {
        return Exercise::class;
    }
}
