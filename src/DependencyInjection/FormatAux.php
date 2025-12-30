<?php

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

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
        return ($boolVal === null && !$returnNull ? false : (bool)$boolVal);
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
                return (is_int($value) || filter_var($value, FILTER_VALIDATE_INT) || $value === '0')
                    ? intval($value) : $value;
            case 'float':
            case 'numeric':
                return filter_var($value, FILTER_VALIDATE_FLOAT) ?
                    floatval($value) : $value;
            default:
                return $value;
        }
    }

    protected static function validateForFormatting(string $nome, int $tamanho, string $value): void
    {
        if (strlen($value) !== $tamanho) {
            throw new InvalidArgumentException("$nome precisa ter $tamanho números!");
        }
        if (!is_numeric($value)) {
            throw new InvalidArgumentException($nome . ' precisa conter apenas números!');
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
            $rp = ($value[1] == 1) ? $ten10[$value[2]] : $unitary[$value[2]];
            $ru = ($value > 0) ? $rp : '';
            $r = $rc . (($rc && ($rd || $ru)) ? ' e ' : '') . $rd . (($rd && $ru) ? ' e ' : '') . $ru;
            $t = count($integer) - 1 - $i;
            $s = $value > 1 ? $plural[$t] : $singular[$t];
            $r .= $r ? ' ' . $s : "";

            if ($value === '000') {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }

            if (($t === 1) && ($z > 0) && ($integer[0] > 0)) {
                $r .= (($z > 1) ? ' de ' : ' ') . $plural[$t];
            }
            if ($r) {
                $st = ($i < $end) ? ', ' : ' e ';
                $accumulator = $accumulator . ((($i > 0) && ($i <= $end) && ($integer[0] > 0) && ($z < 1))
                    ? $st : '') . $r;
            }
        }
        return ($accumulator ? $accumulator : 'zero');
    }
}
