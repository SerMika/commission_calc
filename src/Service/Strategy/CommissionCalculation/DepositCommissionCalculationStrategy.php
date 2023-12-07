<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\OperationType;
use App\Service\Processor\MathProcessorInterface;

class DepositCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
{
    public function __construct(
        private readonly string $depositCommissionFeePercentage,
        private readonly MathProcessorInterface $mathProcessor,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): string
    {
        return $this->mathProcessor->calculatePercentage(
            $operation->getAmount(),
            $this->depositCommissionFeePercentage
        );
    }

    public function supportsOperation(Operation $operation): bool
    {
        return $operation->getType() === OperationType::Deposit;
    }
}
