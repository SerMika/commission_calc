<?php

declare(strict_types=1);

namespace App\Service\Strategy\CommissionCalculation;

use App\DTO\Operation;
use App\Enum\UserType;
use App\Service\CurrencyConverterProcessor;
use App\Service\DateProcessor;
use App\Service\MathProcessor;
use DateTimeImmutable;

class WithdrawCommissionCalculationProcessor implements OperationCommissionCalculationStrategyInterface
{
    private array $usersOperationsPerWeekInfo;
    private ?DateTimeImmutable $lastOperationDate;

    public function __construct(
        private readonly float $withdrawBusinessCommissionFeePercentage,
        private readonly float $withdrawPrivateCommissionFeePercentage,
        private readonly int $freeWithdrawOperationsPerWeekCount,
        private readonly int $freeWithdrawOperationsPerWeekAmountInEur,
        private readonly CurrencyConverterProcessor $currencyConverterService,
    ) {
        $this->usersOperationsPerWeekInfo = [];
        $this->lastOperationDate = null;
    }

    public function calculateCommissionForOperation(Operation $operation): float
    {
        return match ($operation->getUserType()) {
            UserType::Private => $this->calculateCommissionForPrivateWithdraw($operation),
            UserType::Business => $this->calculateCommissionForBusinessWithdraw($operation)
        };
    }

    private function calculateCommissionForPrivateWithdraw(Operation $operation): float
    {
        $this->updateUsersOperationsCountIfNewWeek($operation->getDate());

        $commission = MathProcessor::calculatePercentage(
            $this->calculateChargeableOperationAmount($operation),
            $this->withdrawPrivateCommissionFeePercentage
        );

        $this->updateUserOperationsPerWeekInfo(
            $operation->getUserId(),
            $this->currencyConverterService->convertToEur($operation->getAmount(), $operation->getCurrency())
        );

        $this->updateLastOperationDate($operation->getDate());

        return $commission;
    }

    private function calculateCommissionForBusinessWithdraw(Operation $operation): float
    {
        return MathProcessor::calculatePercentage(
            $operation->getAmount(),
            $this->withdrawBusinessCommissionFeePercentage
        );
    }

    private function updateUsersOperationsCountIfNewWeek(DateTimeImmutable $operationDate): void
    {
        if ($this->lastOperationDate !== null && !DateProcessor::isSameWeek($this->lastOperationDate, $operationDate)) {
            $this->usersOperationsPerWeekInfo = [];
        }
    }

    private function calculateChargeableOperationAmount(Operation $operation): float
    {
        $userId = $operation->getUserId();
        $operationAmount = $operation->getAmount();
        $operationCurrency = $operation->getCurrency();

        if ($this->userIsEligibleForFreeOfChargeOperation($userId)) {
            $operationAmountInEurAfterDiscount = MathProcessor::nonNegativeSubtraction(
                $this->currencyConverterService->convertToEur($operationAmount, $operationCurrency),
                $this->calculateAvailableDiscountAmountForUser($userId)
            );

            return $this->currencyConverterService->convertFromEur(
                $operationAmountInEurAfterDiscount,
                $operationCurrency
            );
        }

        return $operationAmount;
    }

    private function userIsEligibleForFreeOfChargeOperation(int $userId): bool
    {
        return !array_key_exists($userId, $this->usersOperationsPerWeekInfo)
            || $this->usersOperationsPerWeekInfo[$userId]['operationsCount'] < $this->freeWithdrawOperationsPerWeekCount
            && $this->usersOperationsPerWeekInfo[$userId]['totalAmountInEur'] < $this->freeWithdrawOperationsPerWeekAmountInEur;
    }

    private function updateUserOperationsPerWeekInfo(int $userId, float $operationAmountInEur): void
    {
        if (array_key_exists($userId, $this->usersOperationsPerWeekInfo)) {
            ++$this->usersOperationsPerWeekInfo[$userId]['operationsCount'];
            $this->usersOperationsPerWeekInfo[$userId]['totalAmountInEur'] += $operationAmountInEur;
        } else {
            $this->usersOperationsPerWeekInfo[$userId]['operationsCount'] = 1;
            $this->usersOperationsPerWeekInfo[$userId]['totalAmountInEur'] = $operationAmountInEur;
        }
    }

    private function updateLastOperationDate(DateTimeImmutable $operationDate): void
    {
        $this->lastOperationDate = $operationDate;
    }

    private function calculateAvailableDiscountAmountForUser(int $userId): float
    {
        $userOperationsPerWeekTotalAmountInEur = array_key_exists($userId, $this->usersOperationsPerWeekInfo) ?
            $this->usersOperationsPerWeekInfo[$userId]['totalAmountInEur'] :
            0;

        return MathProcessor::nonNegativeSubtraction(
            $this->freeWithdrawOperationsPerWeekAmountInEur,
            $userOperationsPerWeekTotalAmountInEur
        );
    }
}
