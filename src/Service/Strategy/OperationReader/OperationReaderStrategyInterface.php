<?php

declare(strict_types=1);

namespace App\Service\Strategy\OperationReader;

use Generator;

interface OperationReaderStrategyInterface
{
    public function readOperationsFromFile(string $filepath): Generator;
}
