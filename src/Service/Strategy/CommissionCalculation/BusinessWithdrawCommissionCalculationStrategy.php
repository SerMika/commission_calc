<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Service\Processor\MathProcessor;

class BusinessWithdrawCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
{
    public function __construct(
        private readonly float $withdrawBusinessCommissionFeePercentage,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        return MathProcessor::calculatePercentage(
            $operation->getAmount(),
            $this->withdrawBusinessCommissionFeePercentage
        );
    }
}
