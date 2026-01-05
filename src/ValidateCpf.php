<?php

namespace DevUtils;

class ValidateCpf
{
    private const INVALID_SEQUENCES = [
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999',
    ];

    private static function calculateDigit(string $cpf, int $length): int
    {
        $sum = 0;
        $multiplier = $length + 1;

        for ($i = 0; $i < $length; $i++, $multiplier--) {
            $sum += (int) $cpf[$i] * $multiplier;
        }

        $remainder = $sum % 11;
        return ($remainder < 2) ? 0 : 11 - $remainder;
    }

    private static function validateRuleCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf) ?? '';

        if (strlen($cpf) !== 11) {
            return false;
        }

        $firstDigit = self::calculateDigit($cpf, 9);
        if ((int) $cpf[9] !== $firstDigit) {
            return false;
        }

        $secondDigit = self::calculateDigit($cpf, 10);
        return (int) $cpf[10] === $secondDigit;
    }

    private static function validateCpfSequenceInvalidate(string $cpf): bool
    {
        return !in_array($cpf, self::INVALID_SEQUENCES, true);
    }

    private static function cleanCpf(string $cpf): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $cpf) ?? '';
        return str_pad($cleaned, 11, '0', STR_PAD_LEFT);
    }

    public static function validateCpf(string $cpf): bool
    {
        if (empty($cpf)) {
            return false;
        }

        $cpf = self::cleanCpf($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (!self::validateCpfSequenceInvalidate($cpf)) {
            return false;
        }

        return self::validateRuleCpf($cpf);
    }
}
