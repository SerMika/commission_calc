<?php

declare(strict_types=1);

namespace App\Validator;

interface OperationValidatorInterface
{
    public function validateOperationArray(int $index, array $operationProperties): void;
}
