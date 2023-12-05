<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;

class CommissionCalculationContext
{
    private OperationCommissionCalculationStrategyInterface $commissionCalculationStrategy;

    public function __construct(
        private readonly DepositCommissionCalculationStrategy $depositCommissionCalculationStrategy,
        private readonly BusinessWithdrawCommissionCalculationStrategy $businessWithdrawCommissionCalculationStrategy,
    ) {
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        return $this->commissionCalculationStrategy->calculateCommissionForOperation($operation);
    }

    public function setDepositCommissionCalculationStrategy(): void
    {
        $this->setCommissionCalculationStrategy($this->depositCommissionCalculationStrategy);
    }

    public function setBusinessWithdrawCommissionCalculationStrategy(): void
    {
        $this->setCommissionCalculationStrategy($this->businessWithdrawCommissionCalculationStrategy);
    }

    private function setCommissionCalculationStrategy(
        OperationCommissionCalculationStrategyInterface $commissionCalculationStrategy
    ): void {
        $this->commissionCalculationStrategy = $commissionCalculationStrategy;
    }
}
