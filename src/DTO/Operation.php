<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use DateTimeImmutable;

class Operation
{
    public const OPERATION_FIELDS = [
        'date',
        'userId',
        'userType',
        'type',
        'amount',
        'currency',
    ];

    public function __construct(
        private readonly DateTimeImmutable $date,
        private readonly int $userId,
        private readonly UserType $userType,
        private readonly OperationType $type,
        private readonly string $amount,
        private readonly OperationCurrency $currency,
    ) {
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function getType(): OperationType
    {
        return $this->type;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): OperationCurrency
    {
        return $this->currency;
    }
}
