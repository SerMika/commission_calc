<?php

declare(strict_types=1);

namespace App\Service\Reader;

use App\DTO\Operation;
use App\Mapper\OperationMapper;
use App\Service\Strategy\OperationReader\OperationReaderContext;
use App\Validator\OperationValidator;
use Exception;
use Generator;

class OperationReader
{
    public function __construct(
        private readonly OperationReaderContext $operationReaderContext,
        private readonly OperationValidator $operationValidator,
        private readonly OperationMapper $operationMapper,
    ) {
    }

    public function getOperationsFromFile(string $filepath): Generator
    {
        $this->validateFileType($filepath);

        $this->setOperationReaderStrategy($filepath);

        $plainOperationsArray = $this->operationReaderContext->readOperationsFromFile($filepath);

        return $this->makeOperationsFromArray($plainOperationsArray);
    }

    public function makeOperationsFromArray(Generator $plainOperationsArray): Generator
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

    private function setOperationReaderStrategy(string $filepath): void
    {
        $fileExtension = pathinfo($filepath)['extension'];

        switch ($fileExtension) {
            case 'csv':
                $this->operationReaderContext->setCSVReaderStrategy();
                break;
            default:
                throw new Exception("Files of type '$fileExtension' are not supported.");
        }
    }

    private function validateFileType(string $filepath): void
    {
        if (!array_key_exists('extension', pathinfo($filepath))) {
            throw new Exception('Incorrect file provided.');
        }
    }
}
