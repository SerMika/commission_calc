<?php

declare(strict_types=1);

namespace App\Service\Reader;

use App\DTO\Operation;
use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\Strategy\OperationReader\OperationReaderContext;
use App\Service\Validator\OperationValidator;
use DateTimeImmutable;
use Generator;

class OperationReader
{
    public function __construct(
        private readonly OperationReaderContext $operationReaderContext,
        private readonly OperationValidator $operationValidator,
    ) {
    }

    public function getOperationsFromFile(string $filepath): Generator
    {
        $this->setOperationReaderStrategy($filepath);

        $plainOperationsArray = $this->operationReaderContext->readOperationsFromFile($filepath);

        return $this->makeOperationsFromArray($plainOperationsArray);
    }

    public function makeOperationsFromArray(Generator $plainOperationsArray): Generator
    {
        foreach ($plainOperationsArray as $index => $plainOperationInfo) {
            $operationInfo = $this->getFormattedOperationInfo($plainOperationInfo);

            $this->operationValidator->validateOperationArray($index + 1, $operationInfo);

            yield $this->makeOperationFromArray($operationInfo);
        }
    }

    private function getFormattedOperationInfo(array $plainOperationInfo): array
    {
        return array_combine(Operation::OPERATION_FIELDS, array_values($plainOperationInfo));
    }

    private function makeOperationFromArray(array $operationInfo): Operation
    {
        return new Operation(
            new DateTimeImmutable($operationInfo['date']),
            intval($operationInfo['userId']),
            UserType::from($operationInfo['userType']),
            OperationType::from($operationInfo['type']),
            floatval($operationInfo['amount']),
            OperationCurrency::from($operationInfo['currency']),
        );
    }

    private function setOperationReaderStrategy(string $filepath): void
    {
        $fileExtension = pathinfo($filepath)['extension'];

        switch ($fileExtension) {
            case 'csv':
                $this->operationReaderContext->setCSVReaderStrategy();
        }
    }
}
