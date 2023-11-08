<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculation;

use App\DTO\Operation;

interface OperationCommissionCalculationInterface
{
    public function calculateCommissionForOperation(Operation $operation): float;
}
