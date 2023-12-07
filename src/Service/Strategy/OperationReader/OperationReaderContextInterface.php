<?php

declare(strict_types=1);

namespace App\Service\Strategy\OperationReader;

use Generator;

interface OperationReaderContextInterface
{
    public function readOperationsFromFile(string $filepath): Generator;

    public function setStrategyForReadingFromFile(string $filepath): void;
}
