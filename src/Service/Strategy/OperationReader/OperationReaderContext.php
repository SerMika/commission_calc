<?php

declare(strict_types=1);

namespace App\Service\Strategy\OperationReader;

use Exception;
use Generator;

class OperationReaderContext implements OperationReaderContextInterface
{
    /** @var OperationReaderStrategyInterface[] */
    private iterable $operationReaderStrategies;

    private ?OperationReaderStrategyInterface $suitableOperationReaderStrategy;

    public function __construct(iterable $operationReaderStrategies)
    {
        $this->operationReaderStrategies = $operationReaderStrategies;
        $this->suitableOperationReaderStrategy = null;
    }

    public function setStrategyForReadingFromFile(string $filepath): void
    {
        foreach ($this->operationReaderStrategies as $operationReaderStrategy) {
            if ($operationReaderStrategy->supportsFile($filepath)) {
                $this->suitableOperationReaderStrategy = $operationReaderStrategy;
            }
        }

        if ($this->suitableOperationReaderStrategy === null) {
            throw new Exception('This file is not supported for reading.');
        }
    }

    public function readOperationsFromFile(string $filepath): Generator
    {
        return $this->suitableOperationReaderStrategy->readOperationsFromFile($filepath);
    }
}
