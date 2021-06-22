<?php

namespace DevUtils\DependencyInjection;

use DevUtils\ValidateFile;

trait TraitRuleFile
{
    private function validateRuleFile($rule, $field, $label): void
    {
        if (!is_numeric($rule) || ($rule <= 0)) {
            $text = "O parâmetro do validador '$label', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field][0] = $text;
            return;
        }
    }

    protected function validateFileMaxUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'maxUploadSize');

        $this->validateHandleErrorsInArray(
            ValidateFile::validateMaxUploadSize(intval($rule), $value, $message),
            $field
        );
    }

    protected function validateFileMinUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minUploadSize');

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
        $this->validateRuleFile($rule, $field, 'maxFile');

        $validateResult = ValidateFile::validateMaximumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    protected function validateMinimumFileNumbers($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minFile');

        $validateResult = ValidateFile::validateMinimumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    private function validateFileCalculateSize(): ?string
    {
        if (!extension_loaded('gd')) {
            return 'Biblioteca GD não foi encontrada!';
        } elseif (count($_FILES) === 0) {
            return 'Anexo não foi encontrado!';
        }
        return null;
    }

    protected function validateMinWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minWidth');

        $msg = $this->validateFileCalculateSize();

        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMinWidth(intval($rule), $value, $field, $message),
                $field
            );
        }
    }

    protected function validateMaxWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minWidth');

        $msg = $this->validateFileCalculateSize();
        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMaxWidth(intval($rule), $value, $field, $message),
                $field
            );
        }
    }
}
