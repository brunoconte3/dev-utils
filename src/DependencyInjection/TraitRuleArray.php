<?php

namespace DevUtils\DependencyInjection;

trait TraitRuleArray
{
    protected function validateArray(string $field = '', mixed $value = null, ?string $message = ''): void
    {
        if (!is_array($value)) {
            $this->errors[$field] = !empty($message) ? $message : "A variável $field não é um array!";
        }
    }

    protected function validateArrayValues(
        string $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        $ruleArray = explode('-', $rule);

        if (!in_array(trim($value), $ruleArray)) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "O campo $field precisa conter uma das opções [" . str_replace('-', ',', $rule) . '] !';
        }
    }
}
