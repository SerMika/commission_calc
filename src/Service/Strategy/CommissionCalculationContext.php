<?php

declare(strict_types=1);

namespace App\Service\Strategy;

use App\DTO\Operation;

class CommissionCalculationContext
{
    private OperationCommissionCalculationStrategyInterface $commissionCalculationStrategy;

    public function __construct(
        private readonly DepositCommissionCalculationStrategy $depositCommissionCalculationStrategy,
        private readonly WithdrawCommissionCalculationProcessor $withdrawCommissionCalculationService,
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

    private function setCommissionCalculationStrategy(
        OperationCommissionCalculationStrategyInterface $commissionCalculationStrategy
    ): void {
        $this->commissionCalculationStrategy = $commissionCalculationStrategy;
    }
}
