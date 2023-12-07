<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Repository\RepositoryInterface;
use App\Service\Processor\CurrencyConverterProcessorInterface;
use App\Service\Processor\DateProcessor;
use App\Service\Processor\MathProcessorInterface;
use DateTimeImmutable;

class PrivateWithdrawCommissionCalculationStrategy implements OperationCommissionCalculationStrategyInterface
{
    public function __construct(
        private readonly string $withdrawPrivateCommissionFeePercentage,
        private readonly string $freeWithdrawOperationsPerWeekCount,
        private readonly string $freeWithdrawOperationsPerWeekAmountInEur,
        private readonly CurrencyConverterProcessorInterface $currencyConverterProcessor,
        private readonly RepositoryInterface $operationsRepository,
        private readonly MathProcessorInterface $mathProcessor,
    ) {
    }

    public function supportsOperation(Operation $operation): bool
    {
        return $operation->getType() === OperationType::Withdraw && $operation->getUserType() === UserType::Private;
    }

    public function calculateCommissionForOperation(Operation $operation): string
    {
        $this->updateUsersOperationsCountIfNewWeek($operation->getDate());

        $commission = $this->mathProcessor->calculatePercentage(
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

    private function calculateChargeableOperationAmount(Operation $operation): string
    {
        $userId = $operation->getUserId();
        $operationAmount = $operation->getAmount();
        $operationCurrency = $operation->getCurrency();

        if ($this->userIsEligibleForFreeOfChargeOperation($userId)) {
            $operationAmountInEurAfterDiscount = $this->mathProcessor->nonNegativeSubtraction(
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

    private function calculateAvailableDiscountAmountForUser(int $userId): string
    {
        $userWithdrawOperationsPerWeekTotalAmountInEur =
            $this->operationsRepository->getTotalUserWithdrawOperationsAmountInEur($userId);

        return $this->mathProcessor->nonNegativeSubtraction(
            $this->freeWithdrawOperationsPerWeekAmountInEur,
            $userWithdrawOperationsPerWeekTotalAmountInEur
        );
    }
}
