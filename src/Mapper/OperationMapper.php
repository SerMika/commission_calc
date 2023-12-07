<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use DateTimeImmutable;

class OperationMapper implements MapperInterface
{
    public function mapToEntityFromArray(array $properties): Operation
    {
        return new Operation(
            new DateTimeImmutable($properties['date']),
            intval($properties['userId']),
            UserType::from($properties['userType']),
            OperationType::from($properties['type']),
            $properties['amount'],
            OperationCurrency::from($properties['currency']),
        );
    }

    /**
     * @param Operation $entity
     */
    public function mapToArrayFromEntity(object $entity): array
    {
        return [
            'date' => $entity->getDate(),
            'userId' => $entity->getUserId(),
            'userType' => $entity->getUserType(),
            'type' => $entity->getType(),
            'currency' => $entity->getCurrency(),
            'amount' => $entity->getAmount(),
        ];
    }
}
