<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Enum\OperationCurrency;

interface CurrencyConverterProcessorInterface
{
    public function getExchangeRates(): array;

    public function convertToEur(string $amount, OperationCurrency $currency): string;

    public function convertFromEur(string $amount, OperationCurrency $currency): string;
}
