<?php

namespace DevUtils;

use DevUtils\DependencyInjection\Rules;

class Validator extends Rules
{
    private function isNumericArrayOfArrays(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        return count(array_filter(array_keys($data), 'is_numeric')) === count($data)
            && count(array_filter(array_values($data), 'is_array')) === count($data);
    }

    public function set(array $data, array $rules): bool
    {
        $data = (array) json_decode((string) $this->levelSubLevelsArrayReturnJson($data), true);

        if (empty($data)) {
            $this->errors['erro'] = 'informe os dados!';
            return false;
        }

        if ($this->isNumericArrayOfArrays($data)) {
            foreach ($data as $val) {
                $this->validateSubLevelData((array) $val, $rules);
            }
        } else {
            $this->validateSubLevelData($data, $rules);
        }

        return true;
    }

    public function getErros(): array
    {
        return $this->errors ?? [];
    }
}
