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
}
