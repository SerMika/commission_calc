<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Service\Reader\OperationReaderInterface;
use App\Service\Strategy\CommissionCalculation\CommissionCalculationContextInterface;
use Generator;

class CommissionCalculationProcessor implements CommissionCalculationProcessorInterface
{
    public function __construct(
        private readonly OperationReaderInterface $operationReader,
        private readonly CommissionCalculationContextInterface $commissionCalculationContext,
        private readonly CommissionConverterProcessorInterface $commissionConverterProcessor,
    ) {
    }

    public function calculate(string $filepath): Generator
    {
        $operations = $this->operationReader->getOperationsFromFile($filepath);

        foreach ($operations as $operation) {
            $this->commissionCalculationContext->setStrategyForOperation($operation);

            $commission = $this->commissionCalculationContext->calculateCommissionForOperation($operation);

            yield $this->commissionConverterProcessor
                ->getCommissionAmountInTheCurrencyFormat($commission, $operation->getCurrency());
        }
    }
}
