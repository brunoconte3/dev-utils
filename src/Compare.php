<?php

declare(strict_types=1);

namespace DevUtils;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

class Compare
{
    private const DEFAULT_TIMEZONE = 'America/Sao_Paulo';
    private const HOUR_PATTERN = '/^(\d{1,2}):([0-5][0-9]):([0-5][0-9])$/';

    private static function normalizeDateFormat(string $date): string
    {
        $date = trim($date);

        if (ValidateDate::validateDateBrazil($date)) {
            return implode('-', array_reverse(explode('/', $date)));
        }

        if (ValidateDate::validateDateAmerican($date)) {
            return $date;
        }

        throw new InvalidArgumentException("Data inválida: '{$date}'. Formatos aceitos: dd/mm/aaaa ou aaaa-mm-dd.");
    }

    private static function normalizeUrl(string $url): string
    {
        return mb_strtoupper(rtrim($url, '/'), 'UTF-8');
    }

    private static function convertTimeToSeconds(string $time): int
    {
        if (preg_match(self::HOUR_PATTERN, trim($time), $matches) !== 1) {
            throw new InvalidArgumentException("Hora inválida: '{$time}'. Formato aceito: HH:MM:SS.");
        }

        return ((int) $matches[1] * 3600) + ((int) $matches[2] * 60) + (int) $matches[3];
    }

    private static function formatSecondsToTime(int $totalSeconds): string
    {
        $absoluteSeconds = abs($totalSeconds);

        return sprintf(
            '%s%02d:%02d:%02d',
            $totalSeconds < 0 ? '-' : '',
            intdiv($absoluteSeconds, 3600),
            intdiv($absoluteSeconds % 3600, 60),
            $absoluteSeconds % 60,
        );
    }

    public static function daysDifferenceBetweenData(string $dtIni, string $dtFin): string
    {
        // UTC evita que transições de horário de verão distorçam a contagem de dias de calendário.
        $utc = new DateTimeZone('UTC');
        $datetime1 = new DateTimeImmutable(self::normalizeDateFormat($dtIni), $utc);
        $datetime2 = new DateTimeImmutable(self::normalizeDateFormat($dtFin), $utc);

        return $datetime1->diff($datetime2)->format('%R%a');
    }

    public static function startDateLessThanEnd(?string $dtIni, ?string $dtFin): bool
    {
        if ($dtIni === null || trim($dtIni) === '' || $dtFin === null || trim($dtFin) === '') {
            return false;
        }

        return (int) self::daysDifferenceBetweenData($dtIni, $dtFin) >= 0;
    }

    public static function startHourLessThanEnd(
        ?string $hourIni,
        ?string $hourFin,
        string $msg = 'Hora Inicial não pode ser maior que a Hora Final!',
        string $msgEmpty = 'Um ou mais campos horas não foram preenchidos!',
    ): ?string {
        if ($hourIni === null || trim($hourIni) === '' || $hourFin === null || trim($hourFin) === '') {
            return $msgEmpty;
        }

        return str_starts_with(self::differenceBetweenHours($hourIni, $hourFin), '-') ? $msg : null;
    }

    public static function calculateAgeInYears(
        string $date,
        ?DateTimeInterface $reference = null,
        string $timezone = self::DEFAULT_TIMEZONE,
    ): int {
        $zone = new DateTimeZone($timezone);
        $dateBirth = new DateTimeImmutable(self::normalizeDateFormat($date), $zone);
        $now = $reference !== null
            ? DateTimeImmutable::createFromInterface($reference)->setTimezone($zone)
            : new DateTimeImmutable('now', $zone);

        if ($dateBirth > $now) {
            return 0;
        }

        return (int) $dateBirth->diff($now)->format('%y');
    }

    public static function differenceBetweenHours(string $hourIni, string $hourFin): string
    {
        return self::formatSecondsToTime(
            self::convertTimeToSeconds($hourFin) - self::convertTimeToSeconds($hourIni),
        );
    }

    public static function checkDataEquality(
        string $firstValue,
        string $secondValue,
        bool $caseSensitive = true,
    ): bool {
        if ($caseSensitive) {
            return $firstValue === $secondValue;
        }

        return mb_strtolower($firstValue, 'UTF-8') === mb_strtolower($secondValue, 'UTF-8');
    }

    public static function contains(string $value, string $search): bool
    {
        return str_contains($value, $search);
    }

    public static function compareStringFrom(string $search, string $str, int $start, int $length): bool
    {
        return substr($str, $start, $length) === $search;
    }

    public static function beginUrlWith(string $search, string $url): bool
    {
        return str_starts_with(self::normalizeUrl($url), self::normalizeUrl($search));
    }

    public static function finishUrlWith(string $search, string $url): bool
    {
        return str_ends_with(self::normalizeUrl($url), self::normalizeUrl($search));
    }
}
