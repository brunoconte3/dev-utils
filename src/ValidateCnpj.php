<?php

namespace DevUtils;

class ValidateCnpj
{
    private static function validateCnpjSequenceInvalidate(
        string $cnpj,
        string | array | bool $cnpjException = '',
    ): bool {
        if (!ctype_digit($cnpj)) {
            return true;
        }
        $cnpjInvalidate = [
            '00000000000000',
            '11111111111111',
            '22222222222222',
            '33333333333333',
            '44444444444444',
            '55555555555555',
            '66666666666666',
            '77777777777777',
            '88888888888888',
            '99999999999999',
        ];

        if ((empty($cnpjException) || is_bool($cnpjException)) && in_array($cnpj, $cnpjInvalidate, true)) {
            return false;
        }
        if (
            is_string($cnpjException) &&
            in_array($cnpj, $cnpjInvalidate, true) &&
            in_array($cnpjException, $cnpjInvalidate, true)
        ) {
            return true;
        }
        if (is_array($cnpjException) && (count($cnpjException) > 0)) {
            $cnpjExceptionValid = [];
            foreach ($cnpjException as $key => $nrInscricao) {
                $cnpjExceptionValid[$key] = false;
                if (in_array($nrInscricao, $cnpjInvalidate, true) && in_array($cnpj, $cnpjInvalidate, true)) {
                    $cnpjExceptionValid[$key] = true;
                }
            }
            return (in_array(false, $cnpjExceptionValid, true)) ? false : true;
        }
        return true;
    }

    private static function validateRuleCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || !ctype_digit(substr($cnpj, 12, 2))) {
            return false;
        }

        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
            $v = self::cnpjCharValue($cnpj[$i]);
            if ($v < 0) return false;
            $sum += $v * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        $dv1  = ($rest < 2) ? 0 : 11 - $rest;
        if ((int)$cnpj[12] !== $dv1) return false;

        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
            $v = ($i < 12) ? self::cnpjCharValue($cnpj[$i]) : $dv1;
            if ($v < 0) return false;
            $sum += $v * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        $dv2  = ($rest < 2) ? 0 : 11 - $rest;

        return (int)$cnpj[13] === $dv2;
    }

    private static function dealCnpj(string $cnpj): string
    {
        return strtoupper(strval(preg_replace('/[^A-Z0-9]/i', '', $cnpj)));
    }

    private static function cnpjCharValue(string $ch): int
    {
        $o = ord($ch);
        if ($o >= 48 && $o <= 57) return $o - 48;
        if ($o >= 65 && $o <= 90) return $o - 48;
        return -1;
    }

    public static function validateCnpj(string $cnpj, string | array | bool $cnpjException = ''): bool
    {
        if (empty($cnpj)) {
            return false;
        }
        $cnpj = self::dealCnpj($cnpj);
        if (!self::validateCnpjSequenceInvalidate($cnpj, $cnpjException)) {
            return false;
        }
        return self::validateRuleCnpj($cnpj);
    }
}
