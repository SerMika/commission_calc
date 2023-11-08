<?php

declare(strict_types=1);

namespace App\Service;

class DateProcessor
{
    public static function isSameWeek(\DateTimeImmutable $firstDate, \DateTimeImmutable $secondDate): bool
    {
        return $firstDate->format('W') === $secondDate->format('W')
            && $firstDate->modify('+7 days') > $secondDate;
    }
}
