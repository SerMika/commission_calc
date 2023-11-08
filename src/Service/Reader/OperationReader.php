<?php

declare(strict_types=1);

namespace App\Service\Reader;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\Validator\OperationValidator;

class OperationReader
{
    public function __construct(
        private readonly CSVReader $CSVReader,
        private readonly OperationValidator $operationValidator,
    ) {
    }

    /**
     * @return Operation[]
     */
    public function getOperationsFromCSV(string $filepath): array
    {
        $plainOperationsArray = $this->getFormattedOperationsArrayFromCSV($filepath);
        $operationsArray = [];

        foreach ($plainOperationsArray as $index => $operationInfo) {
            $this->operationValidator->validateOperationArray($index + 1, $operationInfo);

            $operationsArray[] = $this->makeOperationFromArray($operationInfo);
        }

        return $operationsArray;
    }

    private function getFormattedOperationsArrayFromCSV($filepath): array
    {
        $operationsArray = $this->CSVReader->readFromCSV($filepath);

        return array_map(function (array $operationArray) {
            return array_combine(Operation::OPERATION_FIELDS, array_values($operationArray));
        }, $operationsArray);
    }

    private function makeOperationFromArray(array $operationInfo): Operation
    {
        return new Operation(
            new \DateTimeImmutable($operationInfo['date']),
            intval($operationInfo['userId']),
            UserType::from($operationInfo['userType']),
            OperationType::from($operationInfo['type']),
            floatval($operationInfo['amount']),
            OperationCurrency::from($operationInfo['currency']),
        );
    }
}
