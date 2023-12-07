<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Enum\OperationCurrency;

class CommissionConverterProcessor implements CommissionConverterProcessorInterface
{
    public function getCommissionAmountInTheCurrencyFormat(float $commission, OperationCurrency $currency): string
    {
        $currencyDecimalPlaces = $currency->decimalPlaces();

        return number_format(
            MathProcessor::roundUpToDecimalPlaces($commission, $currencyDecimalPlaces),
            $currencyDecimalPlaces,
            thousands_separator: ''
        );
    }
}
