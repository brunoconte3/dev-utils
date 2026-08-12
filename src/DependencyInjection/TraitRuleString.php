<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use DevUtils\DependencyInjection\data\DataDdds;
use DevUtils\Format;
use DevUtils\resource\Common;
use DevUtils\ValidateCnpj;
use DevUtils\ValidateCpf;
use DevUtils\ValidatePhone;
use DevUtils\ValidateString;

trait TraitRuleString
{
    protected function validateAlphabets(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (
            preg_match(
                '/^([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value,
            )
        ) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field só pode conter caracteres alfabéticos!";
    }

    protected function validateAlphaNoSpecial(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (preg_match('/^([a-zA-Z\s])+$/', $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field só pode conter caracteres alfabéticos regular, não pode ter ascentos!";
    }

    protected function validateAlphaNumNoSpecial(
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (preg_match('/^([a-zA-Z0-9\s])+$/', $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field só pode conter letras sem acentos e números, não pode carácter especial!";
    }

    protected function validateAlphaNumerics(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (
            preg_match(
                '/^([a-zA-Z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value,
            )
        ) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field só pode conter caracteres alfanuméricos!";
    }

    protected function validateCompanyIdentification(
        string | array $rule = '',
        string $field = '',
        ?string $value = '',
        ?string $message = '',
    ): void {
        if (!empty($value) && ValidateCnpj::validateCnpj($value, $rule)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field é inválido!";
    }

    private function dddListForLength(int $length): array
    {
        $arrayDdd = DataDdds::returnDddBrazil();
        if ($length !== 3) {
            return $arrayDdd;
        }

        return array_map(function ($value) {
            if (is_array($value)) {
                return array_map(function ($item) {
                    return '0' . (string) $item;
                }, $value);
            }
        }, $arrayDdd);
    }

    private function dddBelongsToState(array $arrayDdd, int | string $rule, string $value): bool
    {
        $ruleValues = $arrayDdd[$rule];

        return is_array($ruleValues) && in_array($value, $ruleValues);
    }

    protected function validateDdd(
        int | string $rule = '',
        int | string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        $length = strlen($value);
        if ($length !== 2 && $length !== 3) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "O campo $field deve conter dois ou três dígitos";
            return;
        }

        $arrayDdd = $this->dddListForLength($length);

        if (!empty($rule) && array_key_exists($rule, $arrayDdd)) {
            if ($this->dddBelongsToState($arrayDdd, $rule, $value)) {
                return;
            }
            $this->errors[$field] = !empty($message) ? $message : 'O campo ' . $field .
                ' não é válido para a sigla ' . $rule;
        }

        if (Common::searchLastLayerRecursive($arrayDdd, $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field é um ddd inexistente ou inválido";
    }

    protected function validateEmail(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve ser um endereço de e-mail válido!";
    }

    protected function validateEquals(
        string $rule,
        string $field = '',
        string $value = '',
        ?string $message = '',
        array $data = [],
    ): void {
        if (!isset($data[$rule])) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "Uma regra inválida está sendo aplicada no campo $field, verifique a documentação!";
        } elseif ($value !== $data[$rule]) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "O campo $field é diferente do campo $rule!";
        }
    }

    protected function validateIdentifier(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (strlen($value) === 11) {
            $value = Format::mask('###.###.###-##', $value);
        }
        if (ValidateCpf::validateCpf($value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field é inválido!";
    }

    protected function validateIdentifierOrCompany(
        string | array $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        $value = strtoupper((string) preg_replace('/[^A-Z0-9]/', '', $value));
        $errorMessage = $message !== null && $message !== '' ? $message : "O campo $field é inválido!";

        if ($value === '') {
            $this->errors[$field] = $errorMessage;
            return;
        }

        if (ctype_digit($value) && strlen($value) === 11) {
            $cpfMasked = Format::mask('###.###.###-##', $value);
            if (!ValidateCpf::validateCpf($cpfMasked)) {
                $this->errors[$field] = $errorMessage;
            }
            return;
        }

        if (strlen($value) === 14 && ctype_digit(substr($value, 12, 2))) {
            if (!ValidateCnpj::validateCnpj($value, $rule)) {
                $this->errors[$field] = $errorMessage;
            }
            return;
        }

        $this->errors[$field] = $errorMessage;
    }

    protected function validateIp(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve ser um endereço de IP válido!";
    }

    protected function validateLower(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (ctype_lower(preg_replace('/\W+/', '', $value))) {
            return;
        }

        $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo minúsculo!";
    }

    protected function validateMac(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (filter_var($value, FILTER_VALIDATE_MAC)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve ser um endereço de MAC válido!";
    }

    protected function validateMinimumField(
        string $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (mb_strlen($value) >= $rule) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field precisa conter no mínimo $rule caracteres!";
    }

    protected function validateMinimumWords(
        int $rule,
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (ValidateString::minWords($value, $rule)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field precisa conter no mínimo $rule palavras!";
    }

    protected function validateMaximumWords(
        int $rule,
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (ValidateString::maxWords($value, $rule)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field precisa conter no máximo $rule palavras!";
    }

    protected function validateMaximumField(
        string $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (mb_strlen($value) <= $rule) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field precisa conter no máximo $rule caracteres!";
    }

    protected function validatePlate(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (preg_match('/^[A-Z]{3}-\d{4}+$/', $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve corresponder ao formato AAA-0000!";
    }

    protected function validatePhone(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (is_numeric($value) && in_array(strlen($value), [10, 11])) {
            if (strlen($value) === 10) {
                $value = Format::mask('(##)####-####', $value);
            }
            if (strlen($value) === 11) {
                $value = Format::mask('(##)#####-####', $value);
            }
        }
        if (ValidatePhone::validate($value)) {
            return;
        }

        $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um telefone válido!";
    }

    protected function validateRegex(
        string $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        if (preg_match($rule, $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field precisa conter um valor com formato válido!";
    }

    protected function validateRgbColor(string $field = '', string $value = '', ?string $message = ''): void
    {
        $regra = '([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])';
        $pattern = '/^' . $regra . '( *),( *)' . $regra . '( *),( *)' . $regra . '( *)$/';
        if (preg_match($pattern, $value)) {
            return;
        }

        $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um RGB Color!";
    }

    protected function validateSpace(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (strpos($value, ' ') === false) {
            return;
        }

        $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode conter espaço!";
    }

    protected function validateUpper(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (ctype_upper(preg_replace('/\W+/', '', $value))) {
            return;
        }

        $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo maiúsculo!";
    }

    protected function validateUrl(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve ser um endereço de URL válida!";
    }

    protected function validateZipCode(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('#####-###', $value);
        }
        if (preg_match('/^(\d{2}\d{3}-\d{3})+$/', $value)) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve corresponder ao formato 00000-000!";
    }
}
