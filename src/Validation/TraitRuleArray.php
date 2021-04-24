<?php

namespace devUtils\Validation;

trait TraitRuleArray
{
    protected function validateArray($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_array($value)) {
            $this->errors[$field] = !empty($message) ? $message : "A variável $field não é um array!";
        }
    }

    protected function validateArrayValues($rule = '', $field = '', $value = null, $message = null)
    {
        $ruleArray = explode('-', $rule);

        if (!in_array(trim($value), $ruleArray)) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "O campo $field precisa conter uma das opções [" . str_replace('-', ',', $rule) . '] !';
        }
    }
}
