<?php

namespace DevUtils;

use DateTime;
use DateTimeZone;

class Compare
{
    private static function normalizeDateFormat(string $date): string
    {
        if (str_contains($date, '/')) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        return $date;
    }

    private static function normalizeUrl(string $url): string
    {
        return strtoupper(str_replace('/', '', $url));
    }

    private static function convertTimeToSeconds(string $time): int
    {
        [$hours, $minutes, $seconds] = explode(':', $time);
        return ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
    }

    private static function formatSecondsToTime(int $totalSeconds): string
    {
        $hours = floor($totalSeconds / 3600);
        $remainingSeconds = $totalSeconds - ($hours * 3600);
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds - ($minutes * 60);

        if (str_starts_with((string) $hours, '-')) {
            $formattedHours = '-' . str_pad(substr((string) $hours, 1), 2, '0', STR_PAD_LEFT);
        } else {
            $formattedHours = str_pad((string) $hours, 2, '0', STR_PAD_LEFT);
        }

        $formattedMinutes = str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad((string) $seconds, 2, '0', STR_PAD_LEFT);

        return "{$formattedHours}:{$formattedMinutes}:{$formattedSeconds}";
    }

    public static function daysDifferenceBetweenData(string $dtIni, string $dtFin): string
    {
        $dtIni = self::normalizeDateFormat($dtIni);
        $dtFin = self::normalizeDateFormat($dtFin);

        $datetime1 = new DateTime($dtIni);
        $datetime2 = new DateTime($dtFin);
        $interval = $datetime1->diff($datetime2);

        return $interval->format('%R%a');
    }

    public static function startDateLessThanEnd(?string $dtIni, ?string $dtFin): bool
    {
        if (empty($dtIni) || empty($dtFin)) {
            return false;
        }

        $daysDifference = (int) str_replace('+', '', self::daysDifferenceBetweenData($dtIni, $dtFin));
        return $daysDifference >= 0;
    }

    public static function startHourLessThanEnd(
        string $hourIni,
        string $hourFin,
        string $msg = 'Hora Inicial não pode ser maior que a Hora Final!',
    ): ?string {
        if (empty($hourIni) || empty($hourFin)) {
            return 'Um ou mais campos horas não foram preenchidos!';
        }

        $diff = self::differenceBetweenHours($hourIni, $hourFin);
        if (str_starts_with($diff, '-')) {
            return $msg;
        }

        return null;
    }

    public static function calculateAgeInYears(string $date): int
    {
        $date = self::normalizeDateFormat($date);
        $dateBirth = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
        $dataNow = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $diff = $dataNow->diff($dateBirth);
        return (int) $diff->format('%y');
    }

    public static function differenceBetweenHours(string $hourIni, string $hourFin): string
    {
        $secondsIni = self::convertTimeToSeconds($hourIni);
        $secondsFin = self::convertTimeToSeconds($hourFin);
        $totalSeconds = $secondsFin - $secondsIni;

        return self::formatSecondsToTime($totalSeconds);
    }

    public static function checkDataEquality(
        string $firstValue,
        string $secondValue,
        bool $caseSensitive = true,
    ): bool {
        return $caseSensitive
            ? $firstValue === $secondValue
            : strcasecmp($firstValue, $secondValue) === 0;
    }

    public static function contains(string $value, string $search): bool
    {
        return str_contains($value, $search);
    }

    public static function compareStringFrom(string $search, string $str, int $start, int $length): bool
    {
        return $str === $search || substr($str, $start, $length) === $search;
    }

    public static function beginUrlWith(string $search, string $url): bool
    {
        $normalizedSearch = self::normalizeUrl($search);
        $normalizedUrl = self::normalizeUrl($url);
        return self::compareStringFrom($normalizedSearch, $normalizedUrl, 0, strlen($normalizedSearch));
    }

    public static function finishUrlWith(string $search, string $url): bool
    {
        $normalizedSearch = self::normalizeUrl($search);
        $normalizedUrl = self::normalizeUrl($url);
        $startPosition = strlen($normalizedUrl) - strlen($normalizedSearch);
        return self::compareStringFrom($normalizedSearch, $normalizedUrl, $startPosition, strlen($normalizedUrl));
    }
}
