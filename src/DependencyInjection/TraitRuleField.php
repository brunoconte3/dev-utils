<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use DevUtils\Compare;

trait TraitRuleField
{
    // Suporte ao padrão PIPE, exemplo: 'int|required|min:14|max:14'.
    private function appendPipeRule(array $rulesArray, string $valueRuleConf): array
    {
        $conf = preg_split('/,/', trim($valueRuleConf), 2);
        $ruleArrayConf = explode(':', is_array($conf) ? $conf[0] : '');
        $isRegex = trim(strtolower($ruleArrayConf[0])) === 'regex';

        if (isset($ruleArrayConf[1]) && (strpos($valueRuleConf, ';') > 0) && !$isRegex) {
            $ruleArrayConf[1] = explode(';', $ruleArrayConf[1]);
        }
        if (is_array($conf) && array_key_exists(1, $conf) && !empty($conf[1])) {
            $rulesArray['message'] = trim(strip_tags($conf[1]));
        }

        $keyConf = $ruleArrayConf[0];
        if (is_string($keyConf)) {
            $rulesArray[(string) $keyConf] = $ruleArrayConf[1] ?? true;
        }

        return $rulesArray;
    }

    private function parsePipeRules(string $rules, mixed $value): array
    {
        $rulesConf = explode('|', trim($rules));
        if (in_array('optional', $rulesConf) && (empty($value) || $value === 'null')) {
            return [];
        }

        $rulesArray = [];
        foreach ($rulesConf as $valueRuleConf) {
            $rulesArray = $this->appendPipeRule($rulesArray, $valueRuleConf);
        }

        return $rulesArray;
    }

    //transforma a string json de validação em array para validação
    private function extractRulesArray(mixed $rules, mixed $value): array
    {
        if (!is_string($rules) || empty($rules)) {
            return is_array($rules) ? $rules : [];
        }

        $decoded = json_decode($rules, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($decoded) ? $decoded : [];
        }

        return $this->parsePipeRules($rules, $value);
    }

    private function callFieldRule(
        string|int $field,
        mixed $value,
        string|int $key,
        mixed $val,
        mixed $msgCustomized,
        array $data,
    ): void {
        //chama a função de validação, de cada parametro json
        $method = $this->getValidationMethod((string) $key);
        $customMsg = is_string($msgCustomized) ? $msgCustomized : null;
        $this->callValidationMethod($method, $val, $field, $value, $customMsg, $data);
    }

    private function applyRuleToErrorList(
        string|int $field,
        mixed $value,
        string|int $key,
        mixed $val,
        mixed $msgCustomized,
        array $data,
        array $errorList,
    ): mixed {
        $auxValue = $errorList;

        foreach (array_keys($errorList) as $errorKey) {
            if (!is_array($this->errors[$field]) || !array_key_exists($errorKey, $this->errors[$field])) {
                continue;
            }

            $auxValue = $this->errors[$field][$errorKey];
            if (!empty($auxValue) && is_string($auxValue) && Compare::contains($auxValue, 'obrigatório!')) {
                $this->errors[$field][$errorKey] = 'O campo ' . (string) $field . ' é obrigatório!';
                continue;
            }

            $this->callFieldRule($field, $value, $key, $val, $msgCustomized, $data);
        }

        return $auxValue;
    }

    private function applyFieldRule(
        string|int $field,
        mixed $value,
        string|int $key,
        mixed $val,
        mixed $msgCustomized,
        array $data,
    ): void {
        $auxValue = $this->errors[$field] ?? '';

        if (is_array($auxValue)) {
            $auxValue = $this->applyRuleToErrorList($field, $value, $key, $val, $msgCustomized, $data, $auxValue);
        }

        if (!is_string($auxValue)) {
            return;
        }

        if (!empty($this->errors[$field]) && Compare::contains($auxValue, 'obrigatório!')) {
            $this->errors[$field] = 'O campo ' . (string) $field . ' é obrigatório!';
            return;
        }

        $this->callFieldRule($field, $value, $key, $val, $msgCustomized, $data);
    }

    //irá chamar uma função para cada validação no json de validação, passando o valor para a função
    private function applyFieldRules(string|int $field, mixed $value, array $rulesArray, array $data): void
    {
        $msgCustomized = $rulesArray['message'] ?? null;
        if (array_key_exists('message', $rulesArray)) {
            unset($rulesArray['message']);
        }

        $hasOptional = in_array('optional', $rulesArray);

        foreach ($rulesArray as $key => $val) {
            $val = is_numeric($val) ? (int) $val : $val;
            $ruleValue = !empty($val) || ($val === 0);

            if ($hasOptional && !$ruleValue) {
                continue;
            }
            if (in_array(trim(strtolower((string) $key)), Rules::RULES_WITHOUT_FUNCS)) {
                continue;
            }

            $this->applyFieldRule($field, $value, $key, $val, $msgCustomized, $data);
        }
    }

    private function extractMissingFieldRules(mixed $rules): array
    {
        if (!is_string($rules) || empty($rules)) {
            return is_array($rules) ? $rules : [];
        }

        $decoded = json_decode($rules, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return is_array($decoded) ? $decoded : [];
        }

        //suporte ao padrão PIPE
        //'int|required|min:14|max:14',
        $rulesArray = [];
        foreach (explode('|', trim($rules)) as $valueRuleConf) {
            $ruleArrayConf = explode(':', trim($valueRuleConf));
            $rulesArray[$ruleArrayConf[0]] = $ruleArrayConf[1] ?? true;
        }

        return $rulesArray;
    }

    private function missingFieldMessage(string|int $field, string $jsonRules): string
    {
        $children = array_filter(array_values((array) json_decode($jsonRules, true)), 'is_array');
        if (count($children) === 0) {
            return 'O campo obrigatório ' . (string) $field . ' não foi encontrado nos dados de entrada!';
        }

        return 'O campo: ' . (string) $field
            . ' não foi encontrado nos dados de entrada, indices filhos são obrigatórios!';
    }

    //se o campo é invalido, ele não existe no json de dados no mesmo nivel que a regra
    //aqui valida se na regra há filhos obrigatorios para esse campo
    private function validateMissingField(string|int $field, mixed $rules): array
    {
        $jsonRules = $this->levelSubLevelsArrayReturnJson($this->extractMissingFieldRules($rules));

        if (strpos(trim(strtolower((string) $jsonRules)), 'required') !== false) {
            $this->errors[$field] = $this->missingFieldMessage($field, (string) $jsonRules);
        }

        return $this->errors;
    }
}
