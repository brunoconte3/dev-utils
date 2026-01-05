<?php

namespace DevUtils;

class ValidateCnpj
{
    private const INVALID_SEQUENCES = [
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

    private static function isInvalidSequence(string $cnpj): bool
    {
        return ctype_digit($cnpj) && in_array($cnpj, self::INVALID_SEQUENCES, true);
    }

    private static function isException(string $cnpj, string | array | bool $cnpjException): bool
    {
        if (is_string($cnpjException)) {
            return $cnpj === $cnpjException;
        }

        if (is_array($cnpjException)) {
            return in_array($cnpj, $cnpjException, true);
        }

        return false;
    }

    private static function validateCnpjSequenceInvalidate(
        string $cnpj,
        string | array | bool $cnpjException = '',
    ): bool {
        if (!self::isInvalidSequence($cnpj)) {
            return true;
        }

        return self::isException($cnpj, $cnpjException);
    }

    private static function calculateDigit(string $cnpj, int $length, int $startWeight): int
    {
        $sum = 0;
        $weight = $startWeight;

        for ($i = 0; $i < $length; $i++) {
            $value = self::cnpjCharValue($cnpj[$i]);
            if ($value < 0) {
                return -1;
            }
            $sum += $value * $weight;
            $weight = ($weight === 2) ? 9 : $weight - 1;
        }

        $remainder = $sum % 11;
        return ($remainder < 2) ? 0 : 11 - $remainder;
    }

    private static function validateRuleCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || !ctype_digit(substr($cnpj, 12, 2))) {
            return false;
        }

        $firstDigit = self::calculateDigit($cnpj, 12, 5);
        if ($firstDigit < 0 || (int) $cnpj[12] !== $firstDigit) {
            return false;
        }

        $secondDigit = self::calculateDigit($cnpj . $firstDigit, 13, 6);
        if ($secondDigit < 0 || (int) $cnpj[13] !== $secondDigit) {
            return false;
        }

        return true;
    }

    private static function dealCnpj(string $cnpj): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', $cnpj));
    }

    private static function cnpjCharValue(string $ch): int
    {
        $ascii = ord($ch);

        if ($ascii >= 48 && $ascii <= 57) {
            return $ascii - 48;
        }

        if ($ascii >= 65 && $ascii <= 90) {
            return $ascii - 48;
        }

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
