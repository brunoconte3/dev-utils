<?php

namespace DevUtils\DependencyInjection;

use DevUtils\ValidateFile;

trait TraitRuleFile
{
    protected function validateFileMaxUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule <= 0)) {
            $text = "O parâmetro do validador 'maxUploadSize', deve ser numérico e maior que zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field][0] = $text;
            return;
        }

        $this->validateHandleErrorsInArray(
            ValidateFile::validateMaxUploadSize(intval($rule), $value, $message),
            $field
        );
    }

    protected function validateFileMinUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule <= 0)) {
            $text = "O parâmetro do validador 'minUploadSize', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field][0] = $text;
            return;
        }

        $this->validateHandleErrorsInArray(
            ValidateFile::validateMinUploadSize(intval($rule), $value, $message),
            $field
        );
    }

    protected function validateFileName($field = '', $value = null, $message = null): void
    {
        if (empty($value) || (count($value) <= 0)) {
            $this->errors[$field][0] = !empty($message) ? $message : "O campo $field não pode ser vazio!";
            return;
        }

        $this->validateHandleErrorsInArray(
            ValidateFile::validateFileName($value, $message),
            $field
        );
    }

    protected function validateFileMimeType($rule = '', $field = '', $value = null, $message = null): void
    {
        $this->validateHandleErrorsInArray(ValidateFile::validateMimeType($rule, $value, $message), $field);
    }

    protected function validateFileUploadMandatory($field = '', $value = null, $message = null): void
    {
        $this->validateHandleErrorsInArray(
            ValidateFile::validateFileUploadMandatory($field, $value, $message),
            $field
        );
    }

    protected function validateMaximumFileNumbers($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = intval(trim($rule));

        if (!is_numeric($rule) || ($rule <= 0)) {
            $text = "O parâmetro do validador 'maxFile', deve ser numérico e maior que zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field][0] = $text;
            return;
        }

        $validateResult = ValidateFile::validateMaximumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    protected function validateMinimumFileNumbers($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule < 0)) {
            $text = "O parâmetro do validador 'minFile', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field][0] = $text;
            return;
        }

        $validateResult = ValidateFile::validateMinimumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    private function validateFileCalculateSize($field, $value): ?string
    {
        if (!extension_loaded('gd')) {
            return 'Biblioteca GD não foi encontrada!';
        } elseif (empty($value['name'])) {
            return 'Anexo não foi encontrado!';
        }
        return null;
    }

    protected function validateMinWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $msg = $this->validateFileCalculateSize($field, $value);
        if (!empty($file)) {
            $this->errors[$field][0] = $msg;
        } else {
            $tmpName = $_FILES[$field]['tmp_name'] ?? $_FILES['tmp_name'];
            list($width) = getimagesize($tmpName);
            if ($width < $rule) {
                $this->errors[$field][0] = !empty($message) ?
                    $message : "O campo $field não pode ser menor que $rule pexels!";
            }
        }
    }

    protected function validateMaxWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $msg = $this->validateFileCalculateSize($field, $value);
        if (!empty($file)) {
            $this->errors[$field][0] = $msg;
        } else {
            $tmpName = $_FILES[$field]['tmp_name'] ?? $_FILES['tmp_name'];
            list($width) = getimagesize($tmpName);
            if ($width > $rule) {
                $this->errors[$field][0] = !empty($message) ?
                    $message : "O campo $field não pode ser maior que $rule pexels!";
            }
        }
    }
}
