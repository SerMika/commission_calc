<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\Processor\MathProcessorInterface;

class BusinessWithdrawCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
{
    public function __construct(
        private readonly string $withdrawBusinessCommissionFeePercentage,
        private readonly MathProcessorInterface $mathProcessor,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): string
    {
        return $this->mathProcessor->calculatePercentage(
            $operation->getAmount(),
            $this->withdrawBusinessCommissionFeePercentage
        );
    }

    public function supportsOperation(Operation $operation): bool
    {
        return $operation->getType() === OperationType::Withdraw && $operation->getUserType() === UserType::Business;
    }
}
