<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Service\MathService;

class CommissionCalculationService
{
    public function __construct(
        private readonly DepositCommissionCalculationService $depositCommissionCalculationService,
        private readonly WithdrawCommissionCalculationService $withdrawCommissionCalculationService,
    ) {
    }

    /**
     * @param array<Operation> $operations
     *
     * @return array<string>
     */
    public function getCommissionsForOperations(array $operations): array
    {
        $commissions = [];

        foreach ($operations as $operation) {
            $commissions[] = $this->getFinalCommissionForOperation($operation);
        }

        return $commissions;
    }

    public function getFinalCommissionForOperation(Operation $operation): string
    {
        $commissionService = $this->getCommissionServiceForOperation($operation);

        $commission = $commissionService->calculateCommissionForOperation($operation);

        return $this->getCommissionAmountInTheCurrencyFormat($commission, $operation->getCurrency());
    }

    private function getCommissionServiceForOperation(Operation $operation): OperationCommissionCalculationInterface
    {
        return match ($operation->getType()) {
            OperationType::Deposit => $this->depositCommissionCalculationService,
            OperationType::Withdraw => $this->withdrawCommissionCalculationService,
        };
    }

    private function getCommissionAmountInTheCurrencyFormat(float $commission, OperationCurrency $currency): string
    {
        $currencyDecimalPlaces = $currency->decimalPlaces();

        return number_format(
            MathService::roundUpToDecimalPlaces($commission, $currencyDecimalPlaces),
            $currencyDecimalPlaces,
            thousands_separator: ''
        );
    }
}
