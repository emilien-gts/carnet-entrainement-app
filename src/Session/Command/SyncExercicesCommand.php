<?php

namespace App\Session\Command;

use App\Session\Service\ExerciseSynchronizer;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(self::DEFAULT_NAME)]
final class SyncExercicesCommand extends Command
{
    public const DEFAULT_NAME = 'app:exercise:sync';

    public function __construct(private readonly ExerciseSynchronizer $synchronizer)
    {
        parent::__construct(self::DEFAULT_NAME);
    }

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->synchronizer->run();

        return self::SUCCESS;
    }
}
