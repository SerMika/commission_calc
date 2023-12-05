<?php

declare(strict_types=1);

namespace App\Service\Strategy\OperationReader;

use Generator;

class CSVReaderStrategy implements OperationReaderStrategyInterface
{
    public function readOperationsFromFile(string $filepath): Generator
    {
        if (($open = fopen($filepath, 'r')) !== false) {
            while (($data = fgetcsv($open)) !== false) {
                yield $data;
            }

            fclose($open);
        }
    }
}
