<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Enum\OperationCurrency;

interface CommissionConverterProcessorInterface
{
    public function getCommissionAmountInTheCurrencyFormat(float $commission, OperationCurrency $currency): string;
}
