<?php

namespace DevUtils;

use DevUtils\DependencyInjection\Rules;

class Validator extends Rules
{
    public function set(array $data, array $rules): bool
    {
        $data = json_decode($this->levelSubLevelsArrayReturnJson($data), true);
        if (empty($data)) {
            $this->errors['erro'] = 'informe os dados!';
            return false;
        }
        if (
            count(array_filter(array_keys($data), 'is_numeric')) === count($data)
            &&
            count(array_filter(array_values($data), 'is_array')) === count($data)
        ) {
            foreach ($data as $val) {
                $this->validateSubLevelData($val, $rules);
            }
        } else {
            $this->validateSubLevelData($data, $rules);
        }
        return true;
    }

    public function getErros(): array
    {
        if (empty($this->errors)) {
            return [];
        }
        return $this->errors;
    }
}
