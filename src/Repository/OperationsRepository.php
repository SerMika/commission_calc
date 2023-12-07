<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Mapper\MapperInterface;
use App\Service\Processor\CurrencyConverterProcessorInterface;
use App\Service\Processor\MathProcessorInterface;
use DateTimeImmutable;

class OperationsRepository implements RepositoryInterface
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
        private readonly MapperInterface $operationMapper,
        private readonly CurrencyConverterProcessorInterface $currencyConverterProcessor,
        private readonly MathProcessorInterface $mathProcessor,
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

    /**
     * @param Operation $entity
     */
    public function add(object $entity): void
    {
        $operationProperties = $this->operationMapper->mapToArrayFromEntity($entity);

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

    public function getTotalUserWithdrawOperationsAmountInEur(int $userId): string
    {
        $userWithdrawOperations = $this->getUserWithdrawOperations($userId);

        $operationsAmountsInEur = array_map(function ($operation) {
            return $this->currencyConverterProcessor->convertToEur($operation['amount'], $operation['currency']);
        }, $userWithdrawOperations);

        return array_reduce($operationsAmountsInEur, function ($a, $b) {
            return $this->mathProcessor->add($a, $b);
        }, '0');
    }

    public function getUserWithdrawOperations(int $userId): array
    {
        return array_filter($this->operations, function ($operation) use ($userId) {
            return $operation['userId'] === $userId && $operation['type'] === OperationType::Withdraw;
        });
    }
}
