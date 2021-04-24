<?php

namespace devUtils\Validation;

class ValidateCnpj
{
    /**
     * @param string|array|bool $cnpjException
     */
    private static function validateCnpjSequenceInvalidate(string $cnpj, $cnpjException = ''): bool
    {
        $cnpjInvalidate = [
            '00000000000000', '11111111111111', '22222222222222', '33333333333333', '44444444444444',
            '55555555555555', '66666666666666', '77777777777777', '88888888888888', '99999999999999'
        ];

        if ((empty($cnpjException) || is_bool($cnpjException)) && in_array($cnpj, $cnpjInvalidate)) {
            return false;
        }

        if (is_string($cnpjException)) {
            if (in_array($cnpj, $cnpjInvalidate) && in_array($cnpjException, $cnpjInvalidate)) {
                return true;
            }
        }

        if (is_array($cnpjException) && (count($cnpjException) > 0)) {
            $cnpjExceptionValid = [];
            foreach ($cnpjException as $key => $nrInscricao) {
                $cnpjExceptionValid[$key] = false;
                if (in_array($nrInscricao, $cnpjInvalidate) && in_array($cnpj, $cnpjInvalidate)) {
                    $cnpjExceptionValid[$key] = true;
                }
            }
            return (in_array(false, $cnpjExceptionValid)) ? false : true;
        }

        return true;
    }

    private static function validateRuleCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) > 14) {
            $cnpj = self::dealCnpj($cnpj);
        }

        if (strlen($cnpj) < 14) {
            return false;
        }

        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        if ($cnpj[12] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }
        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        $res = $cnpj[13] == ($rest < 2 ? 0 : 11 - $rest);
        return $res;
    }

    private static function dealCnpj(string $cnpj): string
    {
        $newCnpj = preg_match('/[0-9]/', $cnpj) ?
            str_replace(['-', '.', '/'], '', str_pad($cnpj, 14, '0', STR_PAD_LEFT), $cnpj) : 0;
        return $newCnpj;
    }

    /**
     * @param string|array|bool $cnpjException
     */
    public static function validateCnpj(string $cnpj, $cnpjException = ''): bool
    {
        if (empty($cnpj)) {
            return false;
        }

        if (strlen($cnpj) > 14) {
            $cnpj = self::dealCnpj($cnpj);
        }

        if (!self::validateCnpjSequenceInvalidate($cnpj, $cnpjException)) {
            return false;
        }
        return self::validateRuleCnpj($cnpj);
    }
}
