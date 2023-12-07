<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;

interface OperationCommissionCalculationStrategyInterface
{
    public function calculateCommissionForOperation(Operation $operation): string;

    public function supportsOperation(Operation $operation): bool;
}
