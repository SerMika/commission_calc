<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculation;

use App\DTO\Operation;
use App\Service\MathProcessor;

class DepositCommissionCalculationProcessor implements OperationCommissionCalculationInterface
{
    public function __construct(
        private readonly float $depositCommissionFeePercentage,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        return MathProcessor::calculatePercentage(
            $operation->getAmount(),
            $this->depositCommissionFeePercentage
        );
    }
}
