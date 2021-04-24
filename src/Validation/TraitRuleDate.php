<?php

namespace DevUtils\Validation;

use DevUtils\Format;

trait TraitRuleDate
{
    protected function validateDateBrazil($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('##/##/####', $value);
        }
        if (empty($value) || !ValidateDate::validateDateBrazil($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateDateAmerican($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('####-##-##', $value);
        }
        if (empty($value) || !ValidateDate::validateDateAmerican($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateHour($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateHour::validateHour($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma hora válida!";
        }
    }

    protected function validateTimestamp($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateDate::validateTimeStamp($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um timestamp válido!";
        }
    }

    protected function validateWeekend($rule = '', $field = '', $value = null, $message = null)
    {
        if (strpos($value, '/') > -1) {
            $value = Format::dateAmerican($value);
        }
        $day = date('w', strtotime($value));
        if (in_array($day, [0, 6])) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode ser um Final de Semana!";
        }
    }
}
