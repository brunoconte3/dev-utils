<?php

namespace DevUtils;

use DateTime;

class Compare
{
    public static function daysDifferenceBetweenData(string $dtIni, string $dtFin): string
    {
        if (strpos($dtIni, '/') > -1) {
            $dtIni = implode('-', array_reverse(explode('/', $dtIni)));
        }
        if (strpos($dtFin, '/') > -1) {
            $dtFin = implode('-', array_reverse(explode('/', $dtFin)));
        }

        $datetime1 = new DateTime($dtIni);
        $datetime2 = new DateTime($dtFin);
        $interval = $datetime1->diff($datetime2);

        return $interval->format('%R%a');
    }

    public static function startDateLessThanEnd(
        ?string $dtIni,
        ?string $dtFin
    ): bool {
        if (!empty($dtIni) && !empty($dtFin)) {
            if (str_replace('+', '', self::daysDifferenceBetweenData($dtIni, $dtFin)) < '0') {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    public static function startHourLessThanEnd(
        string $hourIni,
        string $hourFin,
        string $msg = 'Hora Inicial não pode ser maior que a Hora Final!'
    ): ?string {
        if (!empty($hourIni) && !empty($hourFin)) {
            $diff = self::differenceBetweenHours($hourIni, $hourFin);
            if (substr($diff, 0, 1) === '-') {
                return $msg;
            }
        } else {
            return 'Um ou mais campos horas não foram preenchidos!';
        }
        return null;
    }

    public static function calculateAgeInYears(string $date): int
    {
        if (strpos($date, '/') > -1) {
            $date = implode('-', array_reverse(explode('/', $date)));
        }
        $dateBirth = new \DateTime($date, new \DateTimeZone('America/Sao_Paulo'));
        $dataNow = new \DateTime("now", new \DateTimeZone('America/Sao_Paulo'));
        $diff = $dataNow->diff($dateBirth);
        return intval($diff->format("%y"));
    }

    public static function differenceBetweenHours(string $hourIni, string $hourFin): string
    {
        $i = 1;
        $timeTotal = null;
        $times = [$hourFin, $hourIni];

        foreach ($times as $time) {
            $seconds = 0;
            list($h, $m, $s) = explode(':', $time);

            $seconds += intval($h) * 3600;
            $seconds += intval($m) * 60;
            $seconds += intval($s);

            $timeTotal[$i] = $seconds;
            $i++;
        }
        $seconds = $timeTotal[1] - $timeTotal[2];
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = str_pad(strval((floor($seconds / 60))), 2, '0', STR_PAD_LEFT);
        $seconds -= intval($minutes) * 60;

        if (substr(strval($hours), 0, 1) === '-') {
            $hours = '-' . str_pad(substr(strval($hours), 1, 2), 2, '0', STR_PAD_LEFT);
        } else {
            $hours = str_pad(strval($hours), 2, '0', STR_PAD_LEFT);
        }
        return "$hours:$minutes:$seconds";
    }

    public static function checkDataEquality(
        string $firstValue,
        string $secoundValue,
        bool $caseSensitive = true
    ): bool {
        if ($caseSensitive) {
            if ($firstValue !== $secoundValue) {
                return false;
            }
        } else {
            if (0 !== strcasecmp($firstValue, $secoundValue)) {
                return false;
            }
        }
        return true;
    }

    public static function contains(string $value, string $search): bool
    {
        return strpos($value, $search) !== false;
    }

    public static function compareStringFrom(string $search, string $str, int $start, int $length): bool
    {
        if ($str === $search) {
            return true;
        }
        if (substr($str, $start, $length) === $search) {
            return true;
        }
        return false;
    }

    public static function beginUrlWith(string $search, string $url): bool
    {
        $newSearch = strtoupper(str_replace('/', '', $search));
        $urlLessDivideBar = strtoupper(str_replace('/', '', $url));
        return self::compareStringFrom($newSearch, $urlLessDivideBar, 0, strlen($newSearch));
    }

    public static function finishUrlWith(string $search, string $url): bool
    {
        $newSearch = strtoupper(str_replace('/', '', $search));
        $urlLessDivideBar = strtoupper(str_replace('/', '', $url));
        return self::compareStringFrom(
            $newSearch,
            $urlLessDivideBar,
            (strlen($urlLessDivideBar) - strlen($newSearch)),
            strlen($urlLessDivideBar)
        );
    }
}
