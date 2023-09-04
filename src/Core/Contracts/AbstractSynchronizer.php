<?php

namespace App\Core\Contracts;

use App\Core\Helper\SynchronizerHelper;
use Doctrine\ORM\EntityManagerInterface;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use pcrov\JsonReader\JsonReader;

abstract class AbstractSynchronizer
{
    protected static string $filename;
    protected static ?string $classname = null;

    public function __construct(
        protected readonly SynchronizerHelper $helper,
        protected readonly JsonReader $reader,
        protected readonly EntityManagerInterface $em,
        protected readonly string $projectDir
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     * @throws Exception
     */
    public function run(): void
    {
        $this->prepare();
        $this->sync();
        $this->finalize();
    }

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     * @throws Exception
     */
    protected function prepare(): void
    {
        $this->openJson($this->getFilename());

        if ($classname = $this->getClassname()) {
            $this->helper->cleanEntity($classname);
        }
    }

    abstract public function sync(): void;

    protected function finalize(): void
    {
        $this->flushAndClear();
        $this->reader->close();
    }

    abstract protected function getFilename(): string;

    abstract protected function getClassname(): ?string;

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Exception
     */
    protected function openJson(string $filename): void
    {
        $path = \sprintf('%s/data/%s', $this->projectDir, $filename);
        if (!\file_exists($path)) {
            throw new \Exception(\sprintf('File at path "%s" does not exists.', $path));
        }

        $this->reader->open($path);
        $this->reader->read('data');
    }

    protected function flushAndClear(): void
    {
        $this->em->flush();
        $this->em->clear();
    }
}
