<?php

namespace DevUtils\Validation;

use DevUtils\Format;

trait TraitRuleString
{
    protected function validateAlphabets($rule = '', $field = '', $value = null, $message = null)
    {
        if (
            !preg_match(
                '/^([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value
            ) !== false
        ) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfabéticos!";
        }
    }

    protected function validateAlphaNoSpecial($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([a-zA-Z\s])+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfabéticos regular, não pode ter ascentos!";
        }
    }

    protected function validateAlphaNumNoSpecial($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([a-zA-Z0-9\s])+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter letras sem acentos e números, não pode carácter especial!";
        }
    }

    protected function validateAlphaNumerics($rule = '', $field = '', $value = null, $message = null)
    {
        if (
            !preg_match(
                '/^([a-zA-Z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value
            ) !== false
        ) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfanuméricos!";
        }
    }

    protected function validateCompanyIdentification($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 14) {
            $value = Format::mask('##.###.###/####-##', $value);
        }
        if (empty($value) || !ValidateCnpj::validateCnpj($value, $rule)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateEmail($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de e-mail válido!";
        }
    }

    protected function validateIdentifier($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) === 11) {
            $value = Format::mask('###.###.###-##', $value);
        }
        if (!ValidateCpf::validateCpf($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateIdentifierOrCompany($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) === 11) {
            $value = Format::mask('###.###.###-##', $value);
        }
        if (is_numeric($value) && strlen($value) === 14) {
            $value = Format::mask('##.###.###/####-##', $value);
        }
        if (strlen($value) === 14) {
            if (!ValidateCpf::validateCpf($value, $rule)) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field é inválido!";
            }
        }
        if (strlen($value) === 18) {
            if (empty($value) || !ValidateCnpj::validateCnpj($value, $rule)) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field é inválido!";
            }
        }
        if (!in_array(strlen($value), [11, 14, 18])) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field é inválido!";
        }
    }

    protected function validateIp($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de IP válido!";
        }
    }

    protected function validateLower($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ctype_lower(preg_replace('/\W+/', '', $value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo minúsculo!";
        }
    }

    protected function validateMac($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_MAC)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de MAC válido!";
        }
    }

    protected function validateMinimumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) < $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no mínimo $rule caracteres!";
        }
    }

    protected function validateMaximumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) > $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no máximo $rule caracteres!";
        };
    }

    protected function validatePlate($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^[A-Z]{3}-[0-9]{4}+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato AAA-0000!";
        }
    }

    protected function validatePhone($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && in_array(strlen($value), [10, 11])) {
            if (strlen($value) === 10) {
                $value = Format::mask('(##)####-####', $value);
            }
            if (strlen($value) === 11) {
                $value = Format::mask('(##)#####-####', $value);
            }
        }
        if (!ValidatePhone::validate($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um telefone válido!";
        }
    }

    protected function validateRegex($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match($rule, $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter um valor com formato válido!";
        }
    }

    protected function validateRgbColor($rule = '', $field = '', $value = null, $message = null)
    {
        $regra = '([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])';
        $pattern = '/^' . $regra . '( *),( *)' . $regra . '( *),( *)' . $regra . '( *)$/';
        if (!preg_match($pattern, $value) !== false) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um RGB Color!";
        }
    }

    protected function validateSpace($rule = '', $field = '', $value = null, $message = null)
    {
        if (strpos($value, ' ') !== false) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode conter espaço!";
        }
    }

    protected function validateUpper($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ctype_upper(preg_replace('/\W+/', '', $value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo maiúsculo!";
        }
    }

    protected function validateUrl($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de URL válida!";
        }
    }

    protected function validateZipCode($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('#####-###', $value);
        }
        if (!preg_match('/^([0-9]{2}[0-9]{3}-[0-9]{3})+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato 00000-000!";
        }
    }
}
