<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\DTO\Operation;
use App\Service\Reader\OperationReaderInterface;
use App\Service\Strategy\CommissionCalculation\CommissionCalculationContextInterface;
use Generator;

class CommissionCalculationProcessor implements CommissionCalculationProcessorInterface
{
    public function __construct(
        private readonly OperationReaderInterface $operationReader,
        private readonly CommissionCalculationContextInterface $commissionCalculationContext,
        private readonly MathProcessorInterface $mathProcessor,
    ) {
    }

    public function calculate(string $filepath): Generator
    {
        /** @var Generator|Operation[] $operations */
        $operations = $this->operationReader->getOperationsFromFile($filepath);

        foreach ($operations as $operation) {
            $this->mathProcessor->setScale($operation->getCurrency()->decimalPlaces());

            $this->commissionCalculationContext->setStrategyForOperation($operation);

            yield $this->commissionCalculationContext->calculateCommissionForOperation($operation);
        }
    }
}
