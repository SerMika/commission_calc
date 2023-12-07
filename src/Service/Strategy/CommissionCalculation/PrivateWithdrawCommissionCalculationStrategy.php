<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Repository\RepositoryInterface;
use App\Service\Processor\CurrencyConverterProcessorInterface;
use App\Service\Processor\DateProcessor;
use App\Service\Processor\MathProcessor;
use DateTimeImmutable;

class PrivateWithdrawCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
{
    public function __construct(
        private readonly float $withdrawPrivateCommissionFeePercentage,
        private readonly int $freeWithdrawOperationsPerWeekCount,
        private readonly int $freeWithdrawOperationsPerWeekAmountInEur,
        private readonly CurrencyConverterProcessorInterface $currencyConverterProcessor,
        private readonly RepositoryInterface $operationsRepository,
    ) {
    }

    public function supportsOperation(Operation $operation): bool
    {
        return $operation->getType() === OperationType::Withdraw && $operation->getUserType() === UserType::Private;
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        $this->updateUsersOperationsCountIfNewWeek($operation->getDate());

        $commission = MathProcessor::calculatePercentage(
            $this->calculateChargeableOperationAmount($operation),
            $this->withdrawPrivateCommissionFeePercentage
        );

        $this->operationsRepository->add($operation);

        return $commission;
    }

    private function updateUsersOperationsCountIfNewWeek(DateTimeImmutable $operationDate): void
    {
        $lastOperationDate = $this->operationsRepository->getLastPrivateWithdrawDate();

        if ($lastOperationDate !== null && !DateProcessor::isSameWeek($lastOperationDate, $operationDate)) {
            $this->operationsRepository->clearAllOperations();
        }
    }

    private function calculateChargeableOperationAmount(Operation $operation): float
    {
        $userId = $operation->getUserId();
        $operationAmount = $operation->getAmount();
        $operationCurrency = $operation->getCurrency();

        if ($this->userIsEligibleForFreeOfChargeOperation($userId)) {
            $operationAmountInEurAfterDiscount = MathProcessor::nonNegativeSubtraction(
                $this->currencyConverterProcessor->convertToEur($operationAmount, $operationCurrency),
                $this->calculateAvailableDiscountAmountForUser($userId)
            );

            return $this->currencyConverterProcessor->convertFromEur(
                $operationAmountInEurAfterDiscount,
                $operationCurrency
            );
        }

        return $operationAmount;
    }

    private function userIsEligibleForFreeOfChargeOperation(int $userId): bool
    {
        return $this->operationsRepository->getUserWithdrawOperationsCount($userId) < $this->freeWithdrawOperationsPerWeekCount
            && $this->operationsRepository->getTotalUserWithdrawOperationsAmountInEur($userId) < $this->freeWithdrawOperationsPerWeekAmountInEur;
    }

    private function calculateAvailableDiscountAmountForUser(int $userId): float
    {
        $userWithdrawOperationsPerWeekTotalAmountInEur =
            $this->operationsRepository->getTotalUserWithdrawOperationsAmountInEur($userId);

        return MathProcessor::nonNegativeSubtraction(
            $this->freeWithdrawOperationsPerWeekAmountInEur,
            $userWithdrawOperationsPerWeekTotalAmountInEur
        );
    }
}
