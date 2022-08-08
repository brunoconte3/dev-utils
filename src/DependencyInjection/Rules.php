<?php

namespace DevUtils\DependencyInjection;

use DevUtils\Compare;

class Rules
{
    use TraitRule;
    use TraitRuleArray;
    use TraitRuleDate;
    use TraitRuleInteger;
    use TraitRuleFile;
    use TraitRuleString;

    protected array $errors = [];
    public const RULES_WITHOUT_FUNCS = ['convert'];

    private function invalidRule(
        string $rule = '',
        string $field = '',
        mixed $value = null,
        ?string $message = '',
    ) {
        $msg = '';
        if (!empty($rule)) {
            $msg .= $rule;
        }
        if (!empty($value)) {
            $msg .= $value;
        }
        if (!empty($message)) {
            $msg .= $message;
        }
        $msg = "Uma regra inválida está sendo aplicada no campo $field!";
        $this->errors[$field] = $msg;
    }

    private function validateHandleErrorsInArray(array $errorList = [], string $field = ''): void
    {
        if (count($errorList) > 0) {
            if ((is_array($this->errors) && array_key_exists($field, $this->errors))) {
                foreach ($errorList as $error) {
                    array_push($this->errors[$field], $error);
                }
                $this->errors[$field] = array_unique($this->errors[$field]);
            } else {
                $this->errors[$field] = $errorList;
            }
        }
    }

    protected function prepareCharset(string $string = '', string $convert = 'UTF-8', bool $bom = false): string
    {
        $bomchar = pack('H*', 'EFBBBF');
        $string = trim(preg_replace("/^$bomchar/", '', $string));
        static $enclist = [
            'UTF-8', 'ASCII',
            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
            'Windows-1251', 'Windows-1252', 'Windows-1254',
        ];
        $charsetType = mb_detect_encoding($string);
        foreach ($enclist as $item) {
            $converted = iconv($item, $item . '//IGNORE', $string);
            if (md5($converted) == md5($string)) {
                $charsetType = $item;
                break;
            }
        }
        if (strtoupper(trim($charsetType)) != strtoupper(trim($convert))) {
            return ($bom ? $bomchar : '') . iconv($charsetType, $convert . '//TRANSLIT', $string);
        }
        return ($bom ? $bomchar : '') . $string;
    }

    public static function functionsValidation(): array
    {
        $data = self::functionsValidationAtoL();
        $data += self::functionsValidationMtoN();
        $data += self::functionsValidationOtoZ();
        return $data;
    }

    protected function validateOptional(): bool
    {
        return true;
    }

    protected function validateFieldMandatory(string $field = '', mixed $value = null, string $message = null)
    {
        if (is_array($value) && (count($value) <= 0)) {
            return $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
        if (
            !isset($value)
            ||  $value === false
            || (is_string($value) && empty(trim($value))) && strval($value) !== '0'
        ) {
            return $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
    }

    protected function validateFieldType(
        string $rule = '',
        string $field = '',
        mixed $value = null,
        ?string $message = null,
    ) {
        if (in_array(trim(strtolower($rule)), self::RULES_WITHOUT_FUNCS)) {
            return;
        }

        $method = trim(self::functionsValidation()[trim($rule)] ?? 'invalidRule');

        $call = [$this, $method];
        //chama há função de validação, de cada parametro json
        if (is_callable($call, true, $method)) {
            //call_user_func_array($call, [$rule, $field, $value, $message]);
            if (in_array(substr($method, 20), $this->methodsNoRuleValue())) {
                call_user_func_array($call, [$field, $value, $message]);
            } elseif ($method === 'validateEquals') {
                call_user_func_array($call, [$rule, $field, $value, $message]);
            } else {
                call_user_func_array($call, [$rule, $field, $value, $message]);
            }
        } else {
            $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
        }
    }

    protected function levelSubLevelsArrayReturnJson(array $data, bool $recursive = false)
    {
        //funcao recurssiva para tratar array e retornar json valido
        //essa função serve para validar dados com json_encode múltiplos, e indices quebrados na estrutura
        foreach ($data as $key => $val) {
            $key = $this->prepareCharset($key, 'UTF-8');
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
        //se for raiz retorna json
        return strtr(json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        ), ["\r" => '', "\n" => '', "\t" => '', "\\" => '']);
    }

    protected function validateSubLevelData(
        array $data,
        array $rules
    ) {
        //percorre o array de validação para não rodar recurssivamente atoa
        foreach ($rules as $key => $val) {
            //se for um objeto no primeiro nivel, valida recurssivo
            if ((array_key_exists($key, $data) && is_array($data[$key])) && is_array($val)) {
                $this->validateSubLevelData($data[$key], $rules[$key]);
            }
            //valida campos filhos required, porém não existe no array de dados
            if (empty($data) && is_array($val) && (strpos(trim(strtolower(json_encode($val))), 'required'))) {
                $this->errors[$key] = "Não foi encontrado o indice $key, campos filhos são obrigatórios!";
                return false;
            }
            //validação campo a campo
            if (is_string($val)) {
                $this->validateRuleField($key, ($data[$key] ?? null), $val, array_key_exists($key, $data), $data);
            }
        }
        return $rules;
    }

    protected function validateRuleField($field, $value, $rules, $valid = false, $data = [])
    {
        //se o campo é valido, ele existe no json de dados, no mesmo nivel que a regra
        if ($valid) {
            //transforma a string json de validação em array para validação
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    // Suporte ao padrão PIPE, exemplo: 'int|required|min:14|max:14'.
                    $rulesConf = explode('|', trim($rules));
                    if (
                        !in_array('optional', $rulesConf)
                        || (in_array('optional', $rulesConf) && !empty($value) && $value !== 'null')
                    ) {
                        foreach ($rulesConf as $valueRuleConf) {
                            $conf = preg_split('/[\,]/', trim($valueRuleConf), 2);
                            $ruleArrayConf = explode(':', $conf[0] ?? '');
                            $regEx = (trim(strtolower($ruleArrayConf[0])) == 'regex') ? true : false;

                            if (isset($ruleArrayConf[1]) && (strpos($valueRuleConf, ';') > 0) && !$regEx) {
                                $ruleArrayConf[1] = explode(';', $ruleArrayConf[1]);
                            }
                            if (array_key_exists(1, $conf) && !empty($conf[1])) {
                                $rulesArray['mensagem'] = trim(strip_tags($conf[1]));
                            }
                            $rulesArray[$ruleArrayConf[0] ?? (count($rulesArray) + 1)] = $ruleArrayConf[1] ?? true;
                        }
                    }
                }
            }
            $rulesArray = !empty($rulesArray) && is_array($rulesArray) ? $rulesArray : [];
            //irá chamar uma função para cada validação no json de validação, passando o valor para a função
            $msgCustomized = $rulesArray['mensagem'] ?? null;
            if (array_key_exists('mensagem', $rulesArray)) {
                unset($rulesArray['mensagem']);
            }
            foreach ($rulesArray as $key => $val) {
                $ruleValue = (!empty($val) || (intval($val) === 0)) ? true : false;
                if (!in_array('optional', $rulesArray) || (in_array('optional', $rulesArray) && $ruleValue)) {
                    if (in_array(trim(strtolower($key)), self::RULES_WITHOUT_FUNCS)) {
                        continue;
                    }
                    $auxValue = $this->errors[$field] ?? '';
                    if (is_array($auxValue)) {
                        foreach ($this->errors[$field] as $chaveErro => $valueErro) {
                            $auxValue = $this->errors[$field][$chaveErro];
                            if (
                                !empty($auxValue)
                                && (is_string($auxValue) && Compare::contains($auxValue, 'obrigatório!'))
                            ) {
                                $this->errors[$field][$chaveErro] = "O campo {$field} é obrigatório!";
                            } else {
                                $method = trim(Rules::functionsValidation()[trim($key)] ?? 'invalidRule');
                                $call = [$this, $method];
                                //chama a função de validação, de cada parametro json
                                if (is_callable($call, true, $method)) {
                                    if (
                                        in_array(substr($method, 20), $this->methodsNoRuleValue())
                                        || in_array($method, $this->methodsNoRuleValue())
                                    ) {
                                        call_user_func_array($call, [$field, $value, $msgCustomized]);
                                    } elseif (substr($method, 20) === 'validateEquals') {
                                        call_user_func_array($call, [$val, $field, $value, $msgCustomized, $data]);
                                    } else {
                                        call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
                                    }
                                } else {
                                    $this->errors[$field][$chaveErro] = "Há regras de validação não implementadas" .
                                        "no campo $field!";
                                }
                            }
                        }
                    }

                    if (is_string($auxValue)) {
                        if (
                            !empty($this->errors[$field])
                            && (is_string($auxValue) && Compare::contains($auxValue, 'obrigatório!'))
                        ) {
                            $this->errors[$field] = "O campo {$field} é obrigatório!";
                        } else {
                            $method = trim(Rules::functionsValidation()[trim($key)] ?? 'invalidRule');
                            $call = [$this, $method];
                            //chama a função de validação, de cada parametro json
                            if (is_callable($call, true, $method)) {
                                if (
                                    in_array(substr($method, 20), $this->methodsNoRuleValue())
                                    || in_array($method, $this->methodsNoRuleValue())
                                ) {
                                    call_user_func_array($call, [$field, $value, $msgCustomized]);
                                } elseif (substr($method, 20) === 'validateEquals') {
                                    call_user_func_array($call, [$val, $field, $value, $msgCustomized, $data]);
                                } else {
                                    call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
                                }
                            } else {
                                $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
                            }
                        }
                    }
                }
            }
        } else {
            //se o campo é invalido, ele não existe no json de dados no mesmo nivel que a regra
            //aqui valida se na regra há filhos obrigatorios para esse campo
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    //suporte ao padrão PIPE
                    //'int|required|min:14|max:14',
                    $rulesConf = explode('|', trim($rules));
                    foreach ($rulesConf as $valueRuleConf) {
                        $ruleArrayConf =  explode(':', trim($valueRuleConf));
                        $rulesArray[$ruleArrayConf[0] ?? (count($rulesArray) + 1)] = $ruleArrayConf[1] ?? true;
                    }
                    //$this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
                }
            }
            $rulesArray = is_array($rulesArray) ? $rulesArray : [];
            $jsonRules = $this->levelSubLevelsArrayReturnJson($rulesArray);
            $compareA = strpos(trim(strtolower($jsonRules)), 'required');
            if ($compareA !== false) {
                $msg = "O campo $field não foi encontrado nos dados de entrada, indices filhos são obrigatórios!";
                if (count(array_filter(array_values(json_decode($jsonRules, true)), 'is_array')) == 0) {
                    $msg = "O campo obrigátorio $field não foi encontrado nos dados de entrada!";
                }
                $this->errors[$field] = $msg;
            }
            return $this->errors;
        }
    }

    protected function validateBoolean($field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores lógicos. (true, 1, yes)!";
        }
    }

    protected function validateFloating($field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo real(flutuante)!";
        }
    }

    protected function validateJson($field = '', $value = null, $message = null)
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
