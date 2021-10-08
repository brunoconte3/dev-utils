<?php

namespace DevUtils;

class ValidateCpf
{
    private static function validateRuleCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
        if (strlen($cpf) != 11) {
            return false;
        }
        for ($i = 0, $j = 10, $sum = 0; $i < 9; $i++, $j--) {
            $sum += intval($cpf[$i]) * $j;
        }
        $rest = $sum % 11;
        if ($cpf[9] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }
        for ($i = 0, $j = 11, $sum = 0; $i < 10; $i++, $j--) {
            $sum += intval($cpf[$i]) * $j;
        }
        $rest = $sum % 11;
        $res = $cpf[10] == ($rest < 2 ? 0 : 11 - $rest);

        return $res;
    }

    private static function validateCpfSequenceInvalidate(string $cpf): bool
    {
        $cpfInvalidate = [
            '00000000000', '11111111111', '22222222222', '33333333333', '44444444444', '55555555555',
            '66666666666', '77777777777', '88888888888', '99999999999'
        ];
        if (in_array($cpf, $cpfInvalidate)) {
            return false;
        }
        return true;
    }

    private static function dealCpf(string $cpf): string
    {
        $newCpf = preg_match('/[0-9]/', $cpf) ?
            str_replace('-', '', str_replace('.', '', str_pad($cpf, 11, '0', STR_PAD_LEFT), $cpf), $cpf) : 0;
        return $newCpf;
    }

    public static function validateCpf(string $cpf): bool
    {
        if (strlen($cpf) > 11) {
            $cpf = self::dealCpf($cpf);
        }

        if (empty($cpf)) {
            return false;
        }

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (self::validateCpfSequenceInvalidate($cpf)) {
            return self::validateRuleCpf($cpf);
        }
        return false;
    }
}
