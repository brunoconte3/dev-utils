<?php

declare(strict_types=1);

namespace DevUtils;

class ValidateCnpj
{
    private const REGEX_CNPJ = '/^[0-9A-Z]{12}[0-9]{2}\z/';
    private const CHAR_VALUE_OFFSET = 48;
    private const WEIGHT_FIRST_DIGIT = 5;
    private const WEIGHT_SECOND_DIGIT = 6;

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

    private static function cleanCnpj(string $cnpj): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', $cnpj));
    }

    private static function isInvalidSequence(string $cnpj): bool
    {
        return in_array($cnpj, self::INVALID_SEQUENCES, true);
    }

    private static function isException(string $cnpj, string | array | bool $cnpjException): bool
    {
        if (is_string($cnpjException)) {
            return $cnpj === self::cleanCnpj($cnpjException);
        }

        if (is_array($cnpjException)) {
            foreach ($cnpjException as $exception) {
                if (is_string($exception) && $cnpj === self::cleanCnpj($exception)) {
                    return true;
                }
            }
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

    private static function cnpjCharValue(string $ch): int
    {
        return ord($ch) - self::CHAR_VALUE_OFFSET;
    }

    private static function calculateDigit(string $cnpj, int $length, int $startWeight): int
    {
        $sum = 0;
        $weight = $startWeight;

        for ($i = 0; $i < $length; $i++) {
            $sum += self::cnpjCharValue($cnpj[$i]) * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }

    private static function validateRuleCnpj(string $cnpj): bool
    {
        if ((int) $cnpj[12] !== self::calculateDigit($cnpj, 12, self::WEIGHT_FIRST_DIGIT)) {
            return false;
        }

        return (int) $cnpj[13] === self::calculateDigit($cnpj, 13, self::WEIGHT_SECOND_DIGIT);
    }

    public static function validateCnpj(string $cnpj, string | array | bool $cnpjException = ''): bool
    {
        $cnpj = self::cleanCnpj($cnpj);

        if (preg_match(self::REGEX_CNPJ, $cnpj) !== 1) {
            return false;
        }

        if (!self::validateCnpjSequenceInvalidate($cnpj, $cnpjException)) {
            return false;
        }

        return self::validateRuleCnpj($cnpj);
    }
}
