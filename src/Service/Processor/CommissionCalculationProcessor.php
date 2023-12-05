<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\MathProcessor;
use App\Service\Reader\OperationReader;
use App\Service\Strategy\CommissionCalculation\CommissionCalculationContext;
use Generator;

class CommissionCalculationProcessor
{
    public function __construct(
        private readonly OperationReader $operationReader,
        private readonly CommissionCalculationContext $commissionCalculationContext,
    ) {
    }

    public function calculateCommissionsForOperationsInFile(string $filepath): Generator
    {
        $operations = $this->operationReader->getOperationsFromFile($filepath);

        foreach ($operations as $operation) {
            yield $this->calculateFinalCommissionForOperation($operation);
        }
    }

    public function calculateFinalCommissionForOperation(Operation $operation): string
    {
        $this->setCommissionCalculationStrategyForOperation($operation);

        $commission = $this->commissionCalculationContext->calculateCommissionForOperation($operation);

        return $this->getCommissionAmountInTheCurrencyFormat($commission, $operation->getCurrency());
    }

    private function setCommissionCalculationStrategyForOperation(Operation $operation): void
    {
        if ($operation->getType() === OperationType::Deposit) {
            $this->commissionCalculationContext->setDepositCommissionCalculationStrategy();
        } elseif ($operation->getUserType() === UserType::Business) {
            $this->commissionCalculationContext->setBusinessWithdrawCommissionCalculationStrategy();
        } else {
            // return withdraw private strat
        }

        //        return match ($operation->getType()) {
        //            OperationType::Deposit => $this->depositCommissionCalculationService,
        //            OperationType::Withdraw => $this->withdrawCommissionCalculationService,
        //        };
    }

    private function getCommissionAmountInTheCurrencyFormat(float $commission, OperationCurrency $currency): string
    {
        $currencyDecimalPlaces = $currency->decimalPlaces();

        return number_format(
            MathProcessor::roundUpToDecimalPlaces($commission, $currencyDecimalPlaces),
            $currencyDecimalPlaces,
            thousands_separator: ''
        );
    }
}
