<?php

declare(strict_types=1);

namespace DevUtils;

use DateTime;
use DateTimeImmutable;

class ValidateDate
{
    private const ISO_CALENDAR_DATE = '/^\d{4}(-?\d{2}(-?\d{2})?)?$/';
    private const ISO_CALENDAR_TIME = '/^\d{2}(:?\d{2}(:?\d{2}(\.\d+)?)?)?$/';
    private const ISO_TIMEZONE_SUFFIX = '/(Z|[+-]\d{2}(:?\d{2})?)$/';
    private const ISO_DURATION_DATE = '/^(\d+Y)?(\d+M)?(\d+W)?(\d+D)?$/';
    private const ISO_DURATION_TIME = '/^(\d+H)?(\d+M)?(\d+S)?$/';

    private static function validateYear(string $year, string $month, string $day): bool
    {
        return strlen($year) >= 4
            && ctype_digit($month)
            && ctype_digit($day)
            && ctype_digit($year)
            && checkdate((int) $month, (int) $day, (int) $year);
    }

    /**
     * @param array{year: int, month: int, day: int} $order
     */
    private static function validateDateWithSeparator(
        string $date,
        string $separator,
        array $order
    ): bool {
        if (strlen($date) < 8 || $separator === '' || !str_contains($date, $separator)) {
            return false;
        }

        $parts = explode($separator, $date);
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

    private static function matchesCalendarTime(string $time): bool
    {
        if (preg_match(self::ISO_TIMEZONE_SUFFIX, $time, $matches) === 1) {
            $time = substr($time, 0, strlen($time) - strlen($matches[0]));
        }

        return preg_match(self::ISO_CALENDAR_TIME, $time) === 1;
    }

    private static function matchesCalendarPattern(string $input): bool
    {
        $segments = explode('T', $input);
        if (count($segments) > 2 || preg_match(self::ISO_CALENDAR_DATE, $segments[0]) !== 1) {
            return false;
        }

        return !isset($segments[1]) || self::matchesCalendarTime($segments[1]);
    }

    private static function isCalendarDateTime(string $input): bool
    {
        if (!self::matchesCalendarPattern($input)) {
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
        $pattern = '/^(\d{4})-?W(0[1-9]|[1-4]\d|5[0-3])(-?([1-7]))?$/';
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
        if (!str_starts_with($input, 'P') || $input === 'P') {
            return false;
        }

        $segments = explode('T', substr($input, 1));
        if (count($segments) > 2) {
            return false;
        }

        if ($segments[0] !== '' && preg_match(self::ISO_DURATION_DATE, $segments[0]) !== 1) {
            return false;
        }

        if (!isset($segments[1])) {
            return true;
        }

        return $segments[1] !== '' && preg_match(self::ISO_DURATION_TIME, $segments[1]) === 1;
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

    public static function validateDateBrazil(string $date): bool
    {
        return self::validateDateWithSeparator($date, '/', [
            'day' => 0,
            'month' => 1,
            'year' => 2,
        ]);
    }

    public static function validateDateAmerican(string $date): bool
    {
        return self::validateDateWithSeparator($date, '-', [
            'day' => 2,
            'month' => 1,
            'year' => 0,
        ]) || self::validateDateWithSeparator($date, '/', [
            'day' => 1,
            'month' => 0,
            'year' => 2,
        ]);
    }

    public static function validateTimestamp(string $date): bool
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
