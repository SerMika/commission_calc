<?php

declare(strict_types=1);

namespace App\Service\Reader;

use Generator;

interface OperationReaderInterface
{
    public function getOperationsFromFile(string $filepath): Generator;
}
