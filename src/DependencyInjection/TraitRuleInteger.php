<?php

namespace DevUtils\DependencyInjection;

trait TraitRuleInteger
{
    protected function validateInteger(string $field = '', ?string $value = null, ?string $message = ''): void
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field deve ser do tipo inteiro!";
        }
    }

    protected function validateIntegerTyped(
        string $field = '',
        string|int|null $value = null,
        ?string $message = ''
    ): void {
        if (!is_int($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field deve ser do tipado como inteiro!";
        }
    }

    protected function validateNumeric(string $field = '', ?string $value = null, ?string $message = ''): void
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field só pode conter valores numéricos!";
        }
    }

    protected function validateNumMax(
        string $rule = '',
        string $field = '',
        ?string $value = '',
        ?string $message = '',
    ): void {
        if ($value < 0) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ter o valor mínimo de zero!";
        }
        if ($value > $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é permitido até o valor máximo de $rule!";
        }
    }

    protected function validateNumMonth(string $field = '', ?string $value = null, ?string $message = ''): void
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa ser do valor inteiro e maior que 0!";
        } elseif ($value > 12 || $value <= 0 || strlen((string) $value) > 2) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é um mês válido!";
        }
    }

    protected function validateNumMin(
        string $rule = '',
        string $field = '',
        ?string $value = '',
        ?string $message = '',
    ): void {
        if (!is_numeric($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um inteiro!";
        }
        if ((int) $value < $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ter o valor mínimo de $rule!";
        }
        if ((int) $value < 0) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ter o valor mínimo de zero!";
        }
    }
}
