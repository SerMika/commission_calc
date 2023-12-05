<?php

declare(strict_types=1);

namespace App\Service\Strategy;

use App\DTO\Operation;

interface OperationCommissionCalculationStrategyInterface
{
    public function calculateCommissionForOperation(Operation $operation): float;
}
