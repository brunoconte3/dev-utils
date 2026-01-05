<?php

namespace DevUtils;

use DateTime;
use DateTimeImmutable;

class ValidateDate
{
    private static function validateYear(string $ano, string $mes, string $dia): bool
    {
        return strlen($ano) >= 4
            && ctype_digit($mes)
            && ctype_digit($dia)
            && ctype_digit($ano)
            && checkdate((int) $mes, (int) $dia, (int) $ano);
    }

    /**
     * @param array{year: int, month: int, day: int} $order
     */
    private static function validateDateWithSeparator(
        string $data,
        string $separator,
        array $order
    ): bool {
        if (strlen($data) < 8 || $separator === '' || !str_contains($data, $separator)) {
            return false;
        }

        $parts = explode($separator, $data);
        if (count($parts) !== 3) {
            return false;
        }

        $year = $parts[$order['year']] ?? 0;
        $month = $parts[$order['month']] ?? '';
        $day = $parts[$order['day']] ?? '';

        return self::validateYear((string) $year, $month, $day);
    }

    private static function validateDateTimeFormat(string $date, string $format): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d !== false && $d->format($format) === $date;
    }

    private static function isCalendarDateTime(string $input): bool
    {
        $pattern = '/^\d{4}((-?\d{2})(-?\d{2})?)?(T\d{2}(:?\d{2}(:?\d{2}(\.\d+)?)?)?(Z|[+-]\d{2}(:?\d{2})?)?)?$/';
        if (!preg_match($pattern, $input)) {
            return false;
        }

        try {
            $date = new DateTime($input);
            if (str_contains($input, '-')) {
                $parts = explode('T', $input)[0];
                if (count(explode('-', $parts)) === 3 && $date->format('Y-m-d') !== $parts) {
                    return false;
                }
            }
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    private static function isWeekDate(string $input): bool
    {
        $pattern = '/^(\d{4})-?W(0[1-9]|[1-4][0-9]|5[0-3])(-?([1-7]))?$/';
        if (!preg_match($pattern, $input, $matches)) {
            return false;
        }

        $year = (int) $matches[1];
        $week = (int) $matches[2];

        if ($week === 53) {
            $date = new DateTime();
            $date->setISODate($year, 53);
            return $date->format('W') === '53';
        }

        return true;
    }

    private static function isOrdinalDate(string $input): bool
    {
        if (!preg_match('/^(\d{4})-?(\d{3})$/', $input, $matches)) {
            return false;
        }

        $year = (int) $matches[1];
        $dayOfYear = (int) $matches[2];
        $isLeap = ($year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0));

        return $dayOfYear >= 1 && $dayOfYear <= ($isLeap ? 366 : 365);
    }

    private static function isDuration(string $input): bool
    {
        $pattern = '/^P(?!$)(\d+Y)?(\d+M)?(\d+W)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+S)?)?$/';
        return (bool) preg_match($pattern, $input);
    }

    private static function isInterval(string $input): bool
    {
        if (!str_contains($input, '/')) {
            return false;
        }

        $parts = explode('/', $input);
        if (count($parts) !== 2) {
            return false;
        }

        return self::validateDateIso8601($parts[0]) && self::validateDateIso8601($parts[1]);
    }

    public static function validateDateBrazil(string $data): bool
    {
        return self::validateDateWithSeparator($data, '/', [
            'day' => 0,
            'month' => 1,
            'year' => 2,
        ]);
    }

    public static function validateDateAmerican(string $data): bool
    {
        return self::validateDateWithSeparator($data, '-', [
            'year' => 0,
            'month' => 1,
            'day' => 2,
        ]);
    }

    public static function validateTimeStamp(string $date): bool
    {
        return self::validateDateTimeFormat($date, 'Y-m-d H:i:s')
            || self::validateDateTimeFormat($date, 'd/m/Y H:i:s');
    }

    public static function validateDateNotFuture(string $dateAmerican): bool
    {
        try {
            $dateProvided = new DateTimeImmutable($dateAmerican);
            $dateNow = new DateTimeImmutable();
            return $dateProvided <= $dateNow;
        } catch (\Exception) {
            return false;
        }
    }

    public static function validateDateUTCWithoutTimezone(string $date): bool
    {
        return self::validateDateTimeFormat($date, 'Y-m-d\TH:i:s');
    }

    public static function validateDateIso8601(string $input): bool
    {
        if (empty($input)) {
            return false;
        }

        return self::isCalendarDateTime($input)
            || self::isDuration($input)
            || self::isWeekDate($input)
            || self::isOrdinalDate($input)
            || self::isInterval($input);
    }
}
