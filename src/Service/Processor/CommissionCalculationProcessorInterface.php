<?php

declare(strict_types=1);

namespace App\Service\Processor;

use Generator;

interface CommissionCalculationProcessorInterface
{
    public function calculate(string $filepath): Generator;
}
