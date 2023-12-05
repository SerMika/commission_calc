<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Service\Processor\MathProcessor;

class DepositCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
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
