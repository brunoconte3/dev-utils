<?php

namespace DevUtils\DependencyInjection;

use DevUtils\ValidateFile;

trait TraitRuleFile
{
    private function validateRuleFile($rule, $field, $label): void
    {
        if (!is_numeric($rule) || ($rule <= 0)) {
            $text = "O parâmetro do validador '$label', deve ser numérico e maior ou igual a zero!";
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

    private function validateFileCalculateSize($field): ?string
    {
        $imgValid = ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'];
        if (!extension_loaded('gd')) {
            return 'Biblioteca GD não foi encontrada!';
        } elseif (count($_FILES) === 0) {
            return 'Anexo não foi encontrado!';
        } else {
            $msg = 'Para validar minWidth, maxWidth, minHeight e maxHeight o arquivo precisa ser uma imagem!';
            $file = $_FILES[$field] ?? $_FILES;
            foreach ($file as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $valor) {
                        if ($key === 'type' && !empty($valor) && !in_array($valor, $imgValid)) {
                            return $msg;
                        }
                    }
                }
                if ($key === 'type' && !empty($value) && is_string($value) && !in_array($value, $imgValid)) {
                    return $msg;
                }
            }
        }
        return null;
    }

    protected function validateMinWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minWidth');
        $msg = $this->validateFileCalculateSize($field);

        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMinWidth($field, intval($rule), $value, $message),
                $field
            );
        }
    }

    protected function validateMinHeight($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minHeight');
        $msg = $this->validateFileCalculateSize($field);

        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMinHeight($field, intval($rule), $value, $message),
                $field
            );
        }
    }

    protected function validateMaxWidth($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minWidth');
        $msg = $this->validateFileCalculateSize($field);

        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMaxWidth($field, intval($rule), $value, $message),
                $field
            );
        }
    }

    protected function validateMaxHeight($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'maxHeight');
        $msg = $this->validateFileCalculateSize($field);

        if (!empty($msg)) {
            $this->errors[$field][0] = $msg;
        } else {
            $this->validateHandleErrorsInArray(
                ValidateFile::validateMaxHeight($field, intval($rule), $value, $message),
                $field
            );
        }
    }
}
