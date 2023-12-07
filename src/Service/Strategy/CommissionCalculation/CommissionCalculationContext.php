<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use Exception;

class CommissionCalculationContext implements CommissionCalculationContextInterface
{
    /** @var OperationCommissionCalculationStrategyInterface[] */
    private iterable $commissionCalculationStrategies;

    private ?OperationCommissionCalculationStrategyInterface $suitableCommissionCalculationStrategy;

    public function __construct(iterable $commissionCalculationStrategies)
    {
        $this->commissionCalculationStrategies = $commissionCalculationStrategies;
        $this->suitableCommissionCalculationStrategy = null;
    }

    public function setStrategyForOperation(Operation $operation): void
    {
        foreach ($this->commissionCalculationStrategies as $commissionCalculationStrategy) {
            if ($commissionCalculationStrategy->supportsOperation($operation)) {
                $this->suitableCommissionCalculationStrategy = $commissionCalculationStrategy;
            }
        }

        if ($this->suitableCommissionCalculationStrategy === null) {
            throw new Exception('Cannot calculate commission for this operation.');
        }
    }

    public function calculateCommissionForOperation(Operation $operation): string
    {
        return $this->suitableCommissionCalculationStrategy->calculateCommissionForOperation($operation);
    }
}
