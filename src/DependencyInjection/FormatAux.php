<?php

namespace DevUtils\DependencyInjection;

use Exception;

abstract class FormatAux
{
    private const DATA_TYPE_TO_CONVERT = [
        'bool',
        'float',
        'int',
        'numeric',
    ];

    private static function returnTypeBool(mixed $val, bool $returnNull = false): bool
    {
        $boolVal = (is_string($val) ?
            filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val);
        return ($boolVal === null && !$returnNull ? false : $boolVal);
    }

    protected static function returnTypeToConvert(array $rules): ?string
    {
        foreach (self::DATA_TYPE_TO_CONVERT as $type) {
            if (in_array($type, $rules)) {
                return $type;
            }
        }
        return null;
    }

    protected static function executeConvert(string $type, mixed $value): mixed
    {
        switch ($type) {
            case 'bool':
                return self::returnTypeBool($value);
            case 'int':
                if (is_int($value)) {
                    return intval($value);
                } elseif (filter_var($value, FILTER_VALIDATE_INT)) {
                    return intval($value);
                } elseif ($value === '0') {
                    return intval($value);
                } else {
                    return $value;
                }
            case 'float':
            case 'numeric':
                return filter_var($value, FILTER_VALIDATE_FLOAT) ? floatval($value) : $value;
            default:
                return $value;
        }
    }

    protected static function validateForFormatting(string $nome, int $tamanho, string $value): void
    {
        if (strlen($value) !== $tamanho) {
            throw new Exception("$nome precisa ter $tamanho números!");
        }
        if (!is_numeric($value)) {
            throw new Exception($nome . ' precisa conter apenas números!');
        }
    }

    protected static function extensive(float $value = 0): string
    {
        $singular = ['centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão',];
        $plural = ['centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões',];
        $hundred = [
            '', 'cem', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos',
            'oitocentos', 'novecentos',
        ];
        $ten = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa',];
        $ten10 = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezesete', 'dezoito', 'dezenove'];
        $unitary = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove',];

        $z = 0;
        $value = number_format($value, 2, '.', '.');
        $integer = explode('.', $value);
        $count = count($integer);
        $accumulator = '';

        for ($i = 0; $i < $count; $i++) {
            for ($ii = strlen($integer[$i]); $ii < 3; $ii++) {
                $integer[$i] = "0" . $integer[$i];
            }
        }

        $end = count($integer) - ($integer[count($integer) - 1] > 0 ? 1 : 2);

        for ($i = 0; $i < count($integer); $i++) {
            $value = $integer[$i];

            $rc = (($value > 100) && ($value < 200)) ? 'cento' : $hundred[$value[0]];
            $rd = ($value[1] < 2) ? '' : $ten[$value[1]];
            $ru = ($value > 0) ? (($value[1] == 1) ? $ten10[$value[2]] : $unitary[$value[2]]) : '';
            $r = $rc . (($rc && ($rd || $ru)) ? ' e ' : '') . $rd . (($rd && $ru) ? ' e ' : '') . $ru;
            $t = count($integer) - 1 - $i;
            $r .= $r ? ' ' . ($value > 1 ? $plural[$t] : $singular[$t]) : "";

            if ($value === '000') {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }

            if (($t === 1) && ($z > 0) && ($integer[0] > 0)) {
                $r .= (($z > 1) ? ' de ' : ' ') . $plural[$t];
            }
            if ($r) {
                $accumulator = $accumulator . ((($i > 0) && ($i <= $end) && ($integer[0] > 0) && ($z < 1))
                    ? (($i < $end) ? ', ' : ' e ') : '') . $r;
            }
        }
        return ($accumulator ? $accumulator : 'zero');
    }
}
