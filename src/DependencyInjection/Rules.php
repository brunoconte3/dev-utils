<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

class Rules
{
    use TraitRule;
    use TraitRuleArray;
    use TraitRuleDate;
    use TraitRuleInteger;
    use TraitRuleField;
    use TraitRuleFile;
    use TraitRuleString;

    protected array $errors = [];
    public const RULES_WITHOUT_FUNCS = ['convert'];

    private function invalidRule(string|int $field = ''): void
    {
        $this->errors[$field] = "Uma regra inválida está sendo aplicada no campo $field!";
    }

    private function getValidationMethod(string $rule): string
    {
        $functionsValidation = self::functionsValidation();
        $trimmedRule = trim($rule);

        return is_string($functionsValidation[$trimmedRule] ?? null)
            ? trim($functionsValidation[$trimmedRule])
            : '';
    }

    private function callValidationMethod(
        string $method,
        mixed $val,
        string|int $field,
        mixed $value,
        ?string $msgCustomized,
        array $data = []
    ): void {
        if ($method === '') {
            $this->invalidRule($field);
            return;
        }

        $call = [$this, $method];
        if (!is_callable($call, true)) {
            if (is_array($this->errors[$field] ?? null)) {
                $this->errors[$field][$field] = 'Há regras de validação não implementadas no campo '
                    . (string) $field . '!';
            } else {
                $this->errors[$field] = 'Há regras de validação não implementadas no campo ' . (string) $field . '!';
            }
            return;
        }

        if (in_array($method, $this->methodsNoRuleValue())) {
            call_user_func_array($call, [$field, $value, $msgCustomized]);
        } elseif ($method === 'validateEquals') {
            call_user_func_array($call, [$val, $field, $value, $msgCustomized, $data]);
        } else {
            call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
        }
    }

    protected function validateHandleErrorsInArray(array $errorList = [], string $field = ''): void
    {
        if (empty($errorList)) {
            return;
        }

        if (array_key_exists($field, $this->errors)) {
            $currentErrors = $this->errors[$field];
            if (is_array($currentErrors)) {
                foreach ($errorList as $error) {
                    $currentErrors[] = $error;
                }
                $this->errors[$field] = array_unique($currentErrors, SORT_REGULAR);
            }
        } else {
            $this->errors[$field] = $errorList;
        }
    }

    protected function prepareCharset(string $string = '', string $convert = 'UTF-8', bool $bom = false): string
    {
        $bomchar = pack('H*', 'EFBBBF');
        $regex = preg_replace("/^$bomchar/", '', $string) ?? '';
        $string = trim($regex);
        static $enclist = [
            'UTF-8',
            'ASCII',
            'ISO-8859-1',
            'ISO-8859-2',
            'ISO-8859-3',
            'ISO-8859-4',
            'ISO-8859-5',
            'ISO-8859-6',
            'ISO-8859-7',
            'ISO-8859-8',
            'ISO-8859-9',
            'ISO-8859-10',
            'ISO-8859-13',
            'ISO-8859-14',
            'ISO-8859-15',
            'ISO-8859-16',
            'Windows-1251',
            'Windows-1252',
            'Windows-1254',
        ];
        $charsetType = mb_detect_encoding($string);
        if ($charsetType === false) {
            $charsetType = 'UTF-8';
        }
        /** @var array<string> $enclist */
        foreach ($enclist as $item) {
            $converted = iconv($item, $item . '//IGNORE', $string);
            if (is_string($converted) && strcmp($converted, $string) === 0) {
                $charsetType = $item;
                break;
            }
        }
        $convertedString = iconv($charsetType, $convert . '//TRANSLIT', $string);
        if (is_string($convertedString) && strtoupper(trim($charsetType)) != strtoupper(trim($convert))) {
            return ($bom ? $bomchar : '') . $convertedString;
        }
        return ($bom ? $bomchar : '') . $string;
    }

    public static function functionsValidation(): array
    {
        $data = self::functionsValidationAtoL();
        $data += self::functionsValidationMtoN();

        return $data + self::functionsValidationOtoZ();
    }

    protected function validateOptional(): bool
    {
        return true;
    }

    protected function validateFieldMandatory(
        string $field = '',
        mixed $value = null,
        ?string $message = null,
    ): array | string {
        if (is_array($value) && (count($value) <= 0)) {
            return $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
        if (
            !isset($value)
            || $value === false
            || (is_string($value) && empty(trim($value))) && (string) $value !== '0'
        ) {
            return $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
        return [];
    }

    protected function validateFieldType(
        string $rule = '',
        string $field = '',
        mixed $value = null,
        ?string $message = null,
    ): void {
        if (in_array(trim(strtolower($rule)), self::RULES_WITHOUT_FUNCS)) {
            return;
        }

        $method = $this->getValidationMethod($rule);
        //chama há função de validação, de cada parametro json
        $this->callValidationMethod($method, $rule, $field, $value, $message);
    }

    protected function levelSubLevelsArrayReturnJson(array $data, bool $recursive = false): mixed
    {
        //funcao recurssiva para tratar array e retornar json valido
        //essa função serve para validar dados com json_encode múltiplos, e indices quebrados na estrutura
        foreach ($data as $key => $val) {
            $key = $this->prepareCharset((string) $key, 'UTF-8');
            if (is_string($val) && !empty($val)) {
                $arr = json_decode($val, true);
                if (is_array($arr) && (json_last_error() === JSON_ERROR_NONE)) {
                    $val = $arr;
                }
            }
            if (is_array($val)) {
                $data[$key] = $this->levelSubLevelsArrayReturnJson($val, true);
            } elseif (is_string($val)) {
                $data[$key] = $this->prepareCharset(addslashes(strip_tags($val)), 'UTF-8');
            }
        }
        if ($recursive) {
            return $data;
        }
        // Pré-processa cada valor em $data
        $data = $this->preProcess($data);
        //se for raiz retorna json
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE,
        ) ?: '';
        // Remove quebras de linha, tabulações
        return str_replace(["\r", "\n", "\t"], '', $json);
    }

    protected function preProcess(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->preProcess($item);
            }
            return $value;
        }

        if (is_string($value)) {
            return preg_replace('/\\\\(?!["\\\\\/bfnrtu])/', '', $value);
        }
        return $value;
    }

    protected function validateSubLevelData(
        array $data,
        array $rules,
    ): array | bool {
        //percorre o array de validação para não rodar recurssivamente atoa
        foreach ($rules as $key => $val) {
            //se for um objeto no primeiro nivel, valida recurssivo
            if ((array_key_exists($key, $data) && is_array($data[$key])) && is_array($val)) {
                $subData = $data[$key];
                $subRules = $rules[$key];
                if (is_array($subRules)) {
                    $this->validateSubLevelData($subData, $subRules);
                }
            }
            //valida campos filhos required, porém não existe no array de dados
            if (empty($data) && is_array($val) && (strpos(trim(strtolower((string) json_encode($val))), 'required'))) {
                $this->errors[$key] = "Não foi encontrado o indice $key, campos filhos são obrigatórios!";
                return false;
            }
            //validação campo a campo
            if (!is_string($val)) {
                continue;
            }

            $this->validateRuleField($key, ($data[$key] ?? null), $val, array_key_exists($key, $data), $data);
        }
        return $rules;
    }

    protected function validateRuleField(
        string|int $field,
        mixed $value,
        mixed $rules,
        bool $valid = false,
        array $data = [],
    ): array {
        //se o campo é valido, ele existe no json de dados, no mesmo nivel que a regra
        if (!$valid) {
            return $this->validateMissingField($field, $rules);
        }

        $rulesArray = $this->extractRulesArray($rules, $value);
        $this->applyFieldRules($field, $value, empty($rulesArray) ? [] : $rulesArray, $data);

        return [];
    }

    protected function validateBoolean(string $field = '', ?string $value = null, ?string $message = null): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field só pode conter valores lógicos. (true, false, 1, 0, yes, no, on, off)!";
    }

    protected function validateFloating(string $field = '', ?string $value = null, ?string $message = null): void
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return;
        }

        $this->errors[$field] = !empty($message)
            ? $message
            : "O campo $field deve ser do tipo real(flutuante)!";
    }

    protected function validateJson(string $field, mixed $value, ?string $message = null): void
    {
        $value = is_array($value) ? json_encode($value) : $value;
        if (is_string($value)) {
            $arr = json_decode($value, true);
            if (!is_array($arr) || (json_last_error() !== JSON_ERROR_NONE)) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field não contém um json válido!";
            }
        } else {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não contém um json válido!";
        }
    }
}
