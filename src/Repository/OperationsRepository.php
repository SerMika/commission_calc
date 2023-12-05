<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Mapper\OperationMapper;
use App\Service\Processor\CurrencyConverterProcessor;
use DateTimeImmutable;

class OperationsRepository
{
    /** @var array{
     *     date: DateTimeImmutable,
     *     userId: int,
     *     userType: UserType,
     *     type: OperationType,
     *     currency: OperationCurrency,
     *     amount: float,
     * }[]
     */
    private array $operations;

    public function __construct(
        private readonly OperationMapper $operationMapper,
        private readonly CurrencyConverterProcessor $currencyConverterProcessor,
    ) {
        $this->operations = [];
        $this->lastOperationDate = null;
    }

    public function getLastPrivateWithdrawDate(): DateTimeImmutable|null
    {
        $privateWithdrawOperations = array_filter($this->operations, function ($operation) {
            return $operation['userType'] === UserType::Private && $operation['type'] === OperationType::Withdraw;
        });

        $privateWithdrawDates = array_column($privateWithdrawOperations, 'date');

        return empty($privateWithdrawDates) ? null : max($privateWithdrawDates);
    }

    public function addOperation(Operation $operation): void
    {
        $operationProperties = $this->operationMapper->mapToArrayFromEntity($operation);

        $this->operations[] = $operationProperties;
    }

    public function clearAllOperations(): void
    {
        $this->operations = [];
    }

    public function getUserWithdrawOperationsCount(int $userId): int
    {
        return count($this->getUserWithdrawOperations($userId));
    }

    public function getTotalUserWithdrawOperationsAmountInEur(int $userId): float
    {
        $userWithdrawOperations = $this->getUserWithdrawOperations($userId);

        $operationsAmountsInEur = array_map(function ($operation) {
            return $this->currencyConverterProcessor->convertToEur($operation['amount'], $operation['currency']);
        }, $userWithdrawOperations);

        return array_sum($operationsAmountsInEur);
    }

    public function getUserWithdrawOperations(int $userId): array
    {
        return array_filter($this->operations, function ($operation) use ($userId) {
            return $operation['userId'] === $userId && $operation['type'] === OperationType::Withdraw;
        });
    }
}
