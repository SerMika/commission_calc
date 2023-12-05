<?php

declare(strict_types=1);

namespace App\Service\Processor;

class MathProcessor
{
    public static function calculatePercentage(int|float $number, int|float $percentage): float
    {
        return ($percentage / 100) * $number;
    }

    public static function roundUpToDecimalPlaces(float $number, int $decimalPlaces): float
    {
        $decimalPlacesMultiplier = pow(10, $decimalPlaces);

        return ceil($number * $decimalPlacesMultiplier) / $decimalPlacesMultiplier;
    }

    public static function nonNegativeSubtraction(int|float $minuend, int|float $subtrahend): int|float
    {
        $difference = $minuend - $subtrahend;

        return max($difference, 0);
    }
}
