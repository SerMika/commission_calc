<?php

declare(strict_types=1);

namespace App\Enum;

enum OperationCurrency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case JPY = 'JPY';

    public function decimalPlaces(): int
    {
        return match ($this) {
            self::EUR, self::USD => 2,
            self::JPY => 0
        };
    }
}
