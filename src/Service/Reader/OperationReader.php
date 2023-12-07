<?php

declare(strict_types=1);

namespace App\Service\Reader;

use App\DTO\Operation;
use App\Mapper\MapperInterface;
use App\Service\Strategy\OperationReader\OperationReaderContextInterface;
use App\Validator\OperationValidatorInterface;
use Exception;
use Generator;

class OperationReader implements OperationReaderInterface
{
    public function __construct(
        private readonly OperationReaderContextInterface $operationReaderContext,
        private readonly OperationValidatorInterface $operationValidator,
        private readonly MapperInterface $operationMapper,
    ) {
    }

    public function getOperationsFromFile(string $filepath): Generator
    {
        $this->validateFileType($filepath);

        $this->operationReaderContext->setStrategyForReadingFromFile($filepath);

        $plainOperationsArray = $this->operationReaderContext->readOperationsFromFile($filepath);

        return $this->makeOperationsFromArray($plainOperationsArray);
    }

    private function makeOperationsFromArray(Generator $plainOperationsArray): Generator
    {
        foreach ($plainOperationsArray as $index => $plainOperationInfo) {
            $operationInfo = $this->getFormattedOperationInfo($plainOperationInfo);

            $this->operationValidator->validateOperationArray($index + 1, $operationInfo);

            yield $this->operationMapper->mapToEntityFromArray($operationInfo);
        }
    }

    private function getFormattedOperationInfo(array $plainOperationInfo): array
    {
        return array_combine(Operation::OPERATION_FIELDS, array_values($plainOperationInfo));
    }

    private function validateFileType(string $filepath): void
    {
        if (!array_key_exists('extension', pathinfo($filepath))) {
            throw new Exception('Incorrect file provided.');
        }
    }
}
