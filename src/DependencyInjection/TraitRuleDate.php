<?php

namespace DevUtils\DependencyInjection;

use DevUtils\{
    Format,
    ValidateDate,
    ValidateHour,
};

trait TraitRuleDate
{
    protected function validateDateBrazil(string $field = '', string $value = null, ?string $message = ''): void
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('##/##/####', $value);
        }
        if (empty($value) || !ValidateDate::validateDateBrazil($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateDateAmerican(string $field = '', string $value = null, ?string $message = ''): void
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('####-##-##', $value);
        }
        if (empty($value) || !ValidateDate::validateDateAmerican($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateHour(string $field = '', string $value = null, ?string $message = ''): void
    {
        $value = $value ?? '';
        if (!ValidateHour::validateHour($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma hora válida!";
        }
    }

    protected function validateTimestamp(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (!ValidateDate::validateTimeStamp($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um timestamp válido!";
        }
    }

    protected function validateWeekend(string $field = '', string $value = '', ?string $message = ''): void
    {
        if (strpos($value, '/') > -1) {
            $value = Format::dateAmerican($value);
        }
        $day = date('w', (strtotime($value) ?: null));
        if (in_array($day, [0, 6])) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode ser um Final de Semana!";
        }
    }

    protected function validateDateNotFuture(string $field = '', string $value = '', ?string $message = ''): void
    {
        $dateAmerican = Format::dateAmerican($value);
        if (!ValidateDate::validateDateNotFuture($dateAmerican)) {
            $this->errors[$field] = !empty($message) ? $message :
                "O campo $field não pode ser uma data maior que a atual";
        }
    }
}
