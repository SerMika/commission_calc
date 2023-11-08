<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculation;

use App\DTO\Operation;
use App\Service\MathService;

class DepositCommissionCalculationService implements OperationCommissionCalculationInterface
{
    public function __construct(
        private readonly float $depositCommissionFeePercentage,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        return MathService::calculatePercentage(
            $operation->getAmount(),
            $this->depositCommissionFeePercentage
        );
    }
}
