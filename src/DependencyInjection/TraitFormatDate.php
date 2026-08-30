<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use DateTimeImmutable;
use DevUtils\ValidateDate;
use InvalidArgumentException;

trait TraitFormatDate
{
    private static function americanToIsoDate(string $date): string
    {
        if (!str_contains($date, '/')) {
            return $date;
        }

        [$month, $day, $year] = explode('/', $date);

        return $year . '-' . $month . '-' . $day;
    }

    private static function isoDateFromDigits(string $date, bool $brazilianOrder): ?string
    {
        $isoDate = $brazilianOrder
            ? substr($date, 4, 4) . '-' . substr($date, 2, 2) . '-' . substr($date, 0, 2)
            : substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);

        [$year, $month, $day] = explode('-', $isoDate);

        return checkdate((int) $month, (int) $day, (int) $year) ? $isoDate : null;
    }

    private static function toIsoDate(string $date, bool $brazilianOrder): ?string
    {
        if (strlen($date) === 8 && ctype_digit($date)) {
            return self::isoDateFromDigits($date, $brazilianOrder);
        }

        if (ValidateDate::validateDateBrazil($date)) {
            return implode('-', array_reverse(explode('/', $date)));
        }

        if (ValidateDate::validateDateAmerican($date)) {
            return self::americanToIsoDate($date);
        }

        $parts = explode('-', $date);
        if (count($parts) === 3 && ValidateDate::validateDateBrazil(implode('/', $parts))) {
            return implode('-', array_reverse($parts));
        }

        return null;
    }

    private static function createDate(string $method, string $date, bool $brazilianOrder = true): DateTimeImmutable
    {
        $trimmed = trim($date);

        if (strlen($trimmed) < 8 || strlen($trimmed) > 10) {
            throw new InvalidArgumentException("$method precisa conter 8 à 10 dígitos!");
        }

        $isoDate = self::toIsoDate($trimmed, $brazilianOrder);
        if ($isoDate === null) {
            throw new InvalidArgumentException("$method recebeu uma data inválida: '$date'!");
        }

        return new DateTimeImmutable($isoDate);
    }

    public static function dateBrazil(string $date): string
    {
        return self::createDate('dateBrazil', $date)->format('d/m/Y');
    }

    public static function dateAmerican(string $date): string
    {
        return self::createDate('dateAmerican', $date, false)->format('Y-m-d');
    }

    public static function writeDateExtensive(string $date): string
    {
        $timestamp = self::createDate('writeDateExtensive', $date)->getTimestamp();

        return StrfTime::strftime('%A, %d de %B de %Y', $timestamp, 'pt_BR');
    }

    public static function convertTimestampBrazilToAmerican(string $dt): string
    {
        if (!ValidateDate::validateTimestamp($dt)) {
            throw new InvalidArgumentException('Data não é um Timestamp!');
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $dt);
        return $dateTime !== false ? $dateTime->format('Y-m-d H:i:s') : '';
    }
}
