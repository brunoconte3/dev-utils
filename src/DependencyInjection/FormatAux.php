<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

abstract class FormatAux
{
    use TraitExtensiveWords;
    use TraitExtensiveConvert;

    private const DATA_TYPE_TO_CONVERT = ['bool', 'float', 'int', 'numeric',];
    private const MAX_EXTENSIVE_VALUE = 1.0e18;

    private static function returnTypeBool(mixed $val, bool $returnNull = false): bool
    {
        $boolVal = is_string($val)
            ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : (bool) $val;

        return $boolVal === null && !$returnNull ? false : (bool) $boolVal;
    }

    private static function isValidInteger(mixed $value): bool
    {
        return is_int($value) || filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private static function isValidFloat(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    private static function throwInvalidArgumentException(string $message): never
    {
        throw new InvalidArgumentException($message);
    }

    private static function validateLength(string $name, int $length, string $value): void
    {
        if (strlen($value) === $length) {
            return;
        }

        self::throwInvalidArgumentException("$name precisa ter $length números!");
    }

    private static function validateNumeric(string $name, string $value): void
    {
        if (is_numeric($value)) {
            return;
        }

        self::throwInvalidArgumentException("$name precisa conter apenas números!");
    }

    protected static function returnTypeToConvert(array $rules): ?string
    {
        foreach (self::DATA_TYPE_TO_CONVERT as $type) {
            if (in_array($type, $rules, true)) {
                return $type;
            }
        }
        return null;
    }

    protected static function executeConvert(string $type, mixed $value): mixed
    {
        return match ($type) {
            'bool' => self::returnTypeBool($value),
            'int' => self::isValidInteger($value) ? (int) $value : $value,
            'float', 'numeric' => self::isValidFloat($value) ? (float) $value : $value,
            default => $value,
        };
    }

    protected static function validateSurroundingSpaces(string $name, string $value): void
    {
        if (trim($value) === $value) {
            return;
        }

        self::throwInvalidArgumentException("$name não pode conter espaços no início ou no fim!");
    }

    protected static function validateForFormatting(string $name, int $length, string $value): void
    {
        self::validateLength($name, $length, $value);
        self::validateNumeric($name, $value);
    }

    protected static function extensive(float $value = 0): string
    {
        if ($value >= self::MAX_EXTENSIVE_VALUE) {
            self::throwInvalidArgumentException('Valor acima do limite suportado para escrita por extenso!');
        }

        $words = self::getExtensiveWordArrays();
        [$integer, $cents] = self::splitCurrency($value);
        $chunks = self::buildIntegerChunks($integer, $words);

        $parts = [];
        if ($chunks !== []) {
            $parts[] = self::joinChunks($chunks) . self::currencySuffix($chunks, $words);
        }
        if ($cents > 0) {
            $parts[] = self::centsToWords($cents, $words);
        }

        return $parts === [] ? 'zero' : implode(' e ', $parts);
    }
}
