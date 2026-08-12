<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use DevUtils\ValidateFile;

trait TraitRuleFile
{
    private function validateRuleFile(int | string $rule, string $field, ?string $label): void
    {
        if (is_numeric($rule) && ($rule > 0)) {
            return;
        }

        $text = "O parâmetro do validador '$label', deve ser numérico e maior ou igual a zero!";
        if (!isset($this->errors[$field]) || !is_array($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][0] = $text;
    }

    protected function validateFileMaxUploadSize(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'maxUploadSize');

        $this->validateHandleErrorsInArray(
            ValidateFile::validateMaxUploadSize((int) $rule, $value, $message),
            $field,
        );
    }

    protected function validateFileMinUploadSize(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minUploadSize');

        $this->validateHandleErrorsInArray(
            ValidateFile::validateMinUploadSize((int) $rule, $value, $message),
            $field,
        );
    }

    protected function validateFileName(string $field = '', array $value = [], ?string $message = ''): void
    {
        if (empty($value)) {
            if (!isset($this->errors[$field]) || !is_array($this->errors[$field])) {
                $this->errors[$field] = [];
            }
            $this->errors[$field][0] = !empty($message) ? $message : "O campo $field não pode ser vazio!";
            return;
        }

        $this->validateHandleErrorsInArray(
            ValidateFile::validateFileName($value),
            $field,
        );
    }

    protected function validateFileMimeType(
        string | array $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateHandleErrorsInArray(ValidateFile::validateMimeType($rule, $value, $message), $field);
    }

    protected function validateFileUploadMandatory(
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateHandleErrorsInArray(
            ValidateFile::validateFileUploadMandatory($field, $value, $message),
            $field,
        );
    }

    protected function validateMaximumFileNumbers(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $rule = (int) trim($rule);
        $this->validateRuleFile($rule, $field, 'maxFile');

        $validateResult = ValidateFile::validateMaximumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    protected function validateMinimumFileNumbers(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, 'minFile');

        $validateResult = ValidateFile::validateMinimumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    private function isNotImageType(mixed $type, array $imgValid): bool
    {
        if (is_array($type)) {
            foreach ($type as $valor) {
                if (!empty($valor) && !in_array($valor, $imgValid)) {
                    return true;
                }
            }

            return false;
        }

        return !empty($type) && is_string($type) && !in_array($type, $imgValid);
    }

    private function validateFileCalculateSize(string $field): ?string
    {
        if (!extension_loaded('gd')) {
            return 'Biblioteca GD não foi encontrada!';
        }
        if (empty($_FILES)) {
            return 'Anexo não foi encontrado!';
        }

        $file = $_FILES[$field] ?? $_FILES;
        if (!is_iterable($file)) {
            return null;
        }

        $imgValid = ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'];
        foreach ($file as $key => $value) {
            if ($key === 'type' && $this->isNotImageType($value, $imgValid)) {
                return 'Para validar minWidth, maxWidth, minHeight e maxHeight o arquivo precisa ser uma imagem!';
            }
        }

        return null;
    }

    private function validateImageDimension(
        string $rule,
        string $field,
        array $value,
        ?string $message,
        string $validationType,
        callable $validationMethod
    ): void {
        $rule = trim($rule);
        $this->validateRuleFile($rule, $field, $validationType);
        $msg = $this->validateFileCalculateSize($field);

        if (!empty($msg)) {
            if (!isset($this->errors[$field]) || !is_array($this->errors[$field])) {
                $this->errors[$field] = [];
            }
            $this->errors[$field][0] = $msg;
        } else {
            $result = $validationMethod($field, (int) $rule, $value, $message);
            if (is_array($result)) {
                $this->validateHandleErrorsInArray($result, $field);
            }
        }
    }

    protected function validateMinWidth(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateImageDimension(
            $rule,
            $field,
            $value,
            $message,
            'minWidth',
            fn(string $f, ?int $r, array $v, ?string $m): array => ValidateFile::validateMinWidth($f, $r, $v, $m),
        );
    }

    protected function validateMinHeight(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateImageDimension(
            $rule,
            $field,
            $value,
            $message,
            'minHeight',
            fn(string $f, ?int $r, array $v, ?string $m): array => ValidateFile::validateMinHeight($f, $r, $v, $m),
        );
    }

    protected function validateMaxWidth(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateImageDimension(
            $rule,
            $field,
            $value,
            $message,
            'minWidth',
            fn(string $f, ?int $r, array $v, ?string $m): array => ValidateFile::validateMaxWidth($f, $r, $v, $m),
        );
    }

    protected function validateMaxHeight(
        string $rule = '',
        string $field = '',
        array $value = [],
        ?string $message = '',
    ): void {
        $this->validateImageDimension(
            $rule,
            $field,
            $value,
            $message,
            'maxHeight',
            fn(string $f, ?int $r, array $v, ?string $m): array => ValidateFile::validateMaxHeight($f, $r, $v, $m),
        );
    }
}
