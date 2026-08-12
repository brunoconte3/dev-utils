<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitRuleArray
{
    private function setArrayError(string $field, ?string $message, string $defaultMessage): void
    {
        $this->errors[$field] = !empty($message) ? $message : $defaultMessage;
    }

    protected function validateArray(string $field = '', mixed $value = null, ?string $message = ''): void
    {
        if (is_array($value)) {
            return;
        }

        $this->setArrayError($field, $message, "A variável $field não é um array!");
    }

    protected function validateArrayValues(
        string $rule = '',
        string $field = '',
        string $value = '',
        ?string $message = '',
    ): void {
        $ruleArray = explode('-', $rule);
        if (in_array(trim($value), $ruleArray, true)) {
            return;
        }

        $options = str_replace('-', ',', $rule);
        $this->setArrayError($field, $message, "O campo $field precisa conter uma das opções [$options] !");
    }
}
