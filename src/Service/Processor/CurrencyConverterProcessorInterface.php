<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Enum\OperationCurrency;

interface CurrencyConverterProcessorInterface
{
    public function getExchangeRates(): array;

    public function convertToEur(float $amount, OperationCurrency $currency): float;

    public function convertFromEur(float $amount, OperationCurrency $currency): float;
}
