<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;

interface CommissionCalculationContextInterface
{
    public function setStrategyForOperation(Operation $operation): void;

    public function calculateCommissionForOperation(Operation $operation): float;
}
