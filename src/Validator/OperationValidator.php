<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enum\OperationCurrency;
use App\Enum\OperationType;
use App\Enum\UserType;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validateOperationArray(int $index, array $operationInfo): void
    {
        $constraints = $this->getConstraints();

        $violations = $this->validator->validate($operationInfo, $constraints);

        $this->throwExceptionIfValidationNotSuccess($index, $violations);
    }

    private function getConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'date' => [new Assert\NotBlank(), new Assert\Date()],
            'userId' => [new Assert\NotBlank(), new Assert\Type('numeric')],
            'userType' => [new Assert\NotBlank(), new Assert\Choice(array_column(UserType::cases(), 'value'))],
            'type' => [new Assert\NotBlank(), new Assert\Choice(array_column(OperationType::cases(), 'value'))],
            'amount' => [new Assert\NotBlank(), new Assert\Type('numeric'), new Assert\Positive()],
            'currency' => [new Assert\NotBlank(), new Assert\Choice(array_column(OperationCurrency::cases(), 'value'))],
        ], allowExtraFields: false);
    }

    /**
     * @throws Exception
     */
    private function throwExceptionIfValidationNotSuccess(
        int $index,
        ConstraintViolationListInterface $violations
    ) {
        if ($violations->count()) {
            $validationError = $violations->get(0);
            $exceptionMessage = sprintf(
                'There are some errors in line %s of your operations file. %s Invalid value: %s',
                $index,
                $validationError->getMessage(),
                $validationError->getInvalidValue(),
            );

            throw new Exception($exceptionMessage);
        }
    }
}
