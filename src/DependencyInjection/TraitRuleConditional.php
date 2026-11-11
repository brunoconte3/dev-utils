<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitRuleConditional
{
    /**
     * Avalia a condição de `requiredIf:campo;valor1;valor2` contra os dados do mesmo nível.
     *
     * @return bool|null true/false conforme a condição; null quando a regra está malformada
     *                   (sem campo de referência ou sem ao menos um valor).
     */
    private function requiredIfConditionMet(mixed $rule, array $data): ?bool
    {
        $reference = $this->requiredIfReferenceField($rule);
        if ($reference === null) {
            return null;
        }

        if (!array_key_exists($reference, $data)) {
            return false;
        }

        return $this->requiredIfValueMatches($data[$reference], array_slice(array_values((array) $rule), 1));
    }

    private function requiredIfReferenceField(mixed $rule): ?string
    {
        if (!is_array($rule) || count($rule) < 2) {
            return null;
        }

        $reference = array_values($rule)[0];

        return is_string($reference) || is_int($reference) ? trim((string) $reference) : null;
    }

    /**
     * @param array<int|string, mixed> $expectedValues
     */
    private function requiredIfValueMatches(mixed $actual, array $expectedValues): bool
    {
        $actualValues = $this->requiredIfComparableValues($actual);

        foreach ($expectedValues as $expected) {
            if (is_scalar($expected) && in_array(trim((string) $expected), $actualValues, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function requiredIfComparableValues(mixed $value): array
    {
        if (is_bool($value)) {
            return $value ? ['true', '1'] : ['false', '0'];
        }

        return is_scalar($value) ? [trim((string) $value)] : [];
    }

    private function isEmptyRuleValue(mixed $value): bool
    {
        return $value === null
            || $value === []
            || (is_string($value) && trim($value) === '');
    }

    private function requiredIfInvalidMessage(string|int $field, ?string $message): string
    {
        return !empty($message)
            ? $message
            : "Uma regra inválida está sendo aplicada no campo $field, verifique a documentação!";
    }

    private function shouldSkipRulesForRequiredIf(array $rulesArray, mixed $value, array $data): bool
    {
        if (!array_key_exists(self::RULE_REQUIRED_IF, $rulesArray)) {
            return false;
        }

        $conditionMet = $this->requiredIfConditionMet($rulesArray[self::RULE_REQUIRED_IF], $data);
        if ($conditionMet === null) {
            return false;
        }

        return !$conditionMet && $this->isEmptyRuleValue($value);
    }

    protected function validateRequiredIf(
        mixed $rule,
        string $field = '',
        mixed $value = null,
        ?string $message = '',
        array $data = [],
    ): void {
        $conditionMet = $this->requiredIfConditionMet($rule, $data);

        if ($conditionMet === null) {
            $this->errors[$field] = $this->requiredIfInvalidMessage($field, $message);
            return;
        }

        if ($conditionMet) {
            $this->validateFieldMandatory($field, $value, $message);
        }
    }
}
