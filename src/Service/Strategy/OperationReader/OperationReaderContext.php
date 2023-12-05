<?php

declare(strict_types=1);

namespace App\Service\Strategy\OperationReader;

use Generator;

class OperationReaderContext
{
    private OperationReaderStrategyInterface $operationReaderStrategy;

    public function __construct(
        private readonly CSVReaderStrategy $CSVReaderStrategy,
    ) {
    }

    public function readOperationsFromFile(string $filepath): Generator
    {
        return $this->operationReaderStrategy->readOperationsFromFile($filepath);
    }

    public function setCSVReaderStrategy(): void
    {
        $this->setStrategy($this->CSVReaderStrategy);
    }

    private function setStrategy(OperationReaderStrategyInterface $operationReaderStrategy): void
    {
        $this->operationReaderStrategy = $operationReaderStrategy;
    }
}
