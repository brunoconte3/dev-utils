<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitRuleArray
{
    protected function validateArray(string $field = '', mixed $value = null, ?string $message = ''): void
    {
        if (is_array($value)) {
            return;
        }

        $this->setError($field, $message, "A variável $field não é um array!");
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
        $this->setError($field, $message, "O campo $field precisa conter uma das opções [$options] !");
    }
}
