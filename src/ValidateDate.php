<?php

namespace DevUtils;

use DateTime;

class ValidateDate
{
    private static function validateYear(string $ano, string $mes, string $dia): bool
    {
        if (strlen($ano) < 4) {
            return false;
        } else {
            if (ctype_digit($mes) && ctype_digit($dia) && ctype_digit($ano)) {
                if (checkdate($mes, $dia, $ano)) {
                    return true;
                }
            }
            return false;
        }
    }

    public static function validateDateBrazil(string $data): bool
    {
        if (strlen($data) < 8) {
            return false;
        } else {
            if (strpos($data, '/') !== false) {
                $partes = explode('/', $data);
                $dia = $partes[0];
                $mes = $partes[1];
                $ano = isset($partes[2]) ? $partes[2] : 0;

                return self::validateYear($ano, $mes, $dia);
            }
            return false;
        }
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
                $ano = isset($partes[0]) ? $partes[0] : 0;

                return self::validateYear($ano, $mes, $dia);
            }
            return false;
        }
    }

    public static function validateTimeStamp($date): bool
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
}
