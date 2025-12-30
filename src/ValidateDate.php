<?php

namespace DevUtils;

use DateTime;
use DateTimeImmutable;

class ValidateDate
{
    private static function validateYear(string $ano, string $mes, string $dia): bool
    {
        if (strlen($ano) < 4) {
            return false;
        }
        if (
            ctype_digit($mes) &&
            ctype_digit($dia) &&
            ctype_digit($ano) &&
            checkdate(intval($mes), intval($dia), intval($ano))
        ) {
            return true;
        }
        return false;
    }

    private static function isCalendarDateTime(string $input): bool
    {
        $pattern = '/^\d{4}((-?\d{2})(-?\d{2})?)?(T\d{2}(:?\d{2}(:?\d{2}(\.\d+)?)?)?(Z|[+-]\d{2}(:?\d{2})?)?)?$/';
        if (!preg_match($pattern, $input)) {
            return false;
        }
        try {
            $date = new \DateTime($input);
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
        $year = (int)$matches[1];
        $week = (int)$matches[2];
        if ($week === 53) {
            $date = new \DateTime();
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
        $year = (int)$matches[1];
        $dayOfYear = (int)$matches[2];
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
        return (self::validateDateIso8601($parts[0]) && self::validateDateIso8601($parts[1]));
    }

    public static function validateDateBrazil(string $data): bool
    {
        if (strlen($data) < 8) {
            return false;
        }
        if (strpos($data, '/') !== false) {
            $partes = explode('/', $data);
            $dia = $partes[0];
            $mes = $partes[1];
            $ano = isset($partes[2]) ? $partes[2] : 0;
            return self::validateYear(strval($ano), $mes, $dia);
        }
        return false;
    }

    public static function validateDateAmerican(string $data): bool
    {
        if (strlen($data) < 8) {
            return false;
        } else {
            if (strpos($data, '-') !== false) {
                $partes = explode('-', $data);
                $dia = $partes[2];
                $mes = $partes[1];
                $ano = $partes[0] ?: 0;

                return self::validateYear(strval($ano), $mes, $dia);
            }
            return false;
        }
    }

    public static function validateTimeStamp(string $date): bool
    {
        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $date);
        $return = $d && $d->format($format) === $date;

        if (!$return) {
            $format = 'd/m/Y H:i:s';
            $d = DateTime::createFromFormat($format, $date);
            $return = $d && $d->format($format) === $date;
        }
        return $return;
    }

    public static function validateDateNotFuture(string $dateAmerican): bool
    {
        $dateAmerican = new DateTimeImmutable($dateAmerican);
        $dateNow = new DateTimeImmutable();
        $interval = $dateAmerican->diff($dateNow);
        $diff = $interval->format('%R%');
        if ($diff === '-') {
            return false;
        }
        return true;
    }

    public static function validateDateUTCWithoutTimezone(string $date): bool
    {
        $format = 'Y-m-d\TH:i:s';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function validateDateIso8601(string $input): bool
    {
        if (empty($input)) {
            return false;
        }
        return self::isCalendarDateTime($input) || self::isDuration($input) || self::isWeekDate($input)
            || self::isOrdinalDate($input) || self::isInterval($input);
    }
}
