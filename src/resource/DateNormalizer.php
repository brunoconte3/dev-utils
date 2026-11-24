<?php

declare(strict_types=1);

namespace DevUtils\resource;

use DevUtils\ValidateDate;

final class DateNormalizer
{
    private static function americanToIsoDate(string $date): string
    {
        if (!str_contains($date, '/')) {
            return $date;
        }

        [$month, $day, $year] = explode('/', $date);

        return $year . '-' . $month . '-' . $day;
    }

    public static function toIsoDate(string $date): ?string
    {
        if (ValidateDate::validateDateBrazil($date)) {
            return implode('-', array_reverse(explode('/', $date)));
        }

        if (ValidateDate::validateDateAmerican($date)) {
            return self::americanToIsoDate($date);
        }

        return null;
    }
}
