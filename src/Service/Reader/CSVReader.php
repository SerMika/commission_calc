<?php

declare(strict_types=1);

namespace App\Service\Reader;

class CSVReader
{
    public function readFromCSV(string $filepath): array
    {
        $result = [];

        if (($open = fopen($filepath, 'r')) !== false) {
            while (($data = fgetcsv($open)) !== false) {
                $result[] = $data;
            }

            fclose($open);
        }

        return $result;
    }
}
