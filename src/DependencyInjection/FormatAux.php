<?php

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

abstract class FormatAux
{
    private const DATA_TYPE_TO_CONVERT = ['bool', 'float', 'int', 'numeric',];
    private const DECIMAL_PLACES = 2;
    private const DECIMAL_SEPARATOR = '.';
    private const THOUSANDS_SEPARATOR = '.';
    private const PADDING_LENGTH = 3;
    private const PADDING_CHAR = '0';

    private static function returnTypeBool(mixed $val, bool $returnNull = false): bool
    {
        $boolVal = is_string($val)
            ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : (bool) $val;

        return $boolVal === null && !$returnNull ? false : (bool) $boolVal;
    }

    private static function isValidInteger(mixed $value): bool
    {
        return is_int($value) || filter_var($value, FILTER_VALIDATE_INT) !== false || $value === '0';
    }

    private static function isValidFloat(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * @return array<int, string>
     */
    private static function getSingularScaleWords(): array
    {
        return ['centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão',];
    }

    /**
     * @return array<int, string>
     */
    private static function getPluralScaleWords(): array
    {
        return ['centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões',];
    }

    /**
     * @return array<int, string>
     */
    private static function getHundredWords(): array
    {
        return [
            '',
            'cem',
            'duzentos',
            'trezentos',
            'quatrocentos',
            'quinhentos',
            'seiscentos',
            'setecentos',
            'oitocentos',
            'novecentos',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getTenWords(): array
    {
        return ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa',];
    }

    /**
     * @return array<int, string>
     */
    private static function getTen10Words(): array
    {
        return ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezesete', 'dezoito', 'dezenove',];
    }

    /**
     * @return array<int, string>
     */
    private static function getUnitaryWords(): array
    {
        return ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove',];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function getExtensiveWordArrays(): array
    {
        return [
            'singular' => self::getSingularScaleWords(),
            'plural' => self::getPluralScaleWords(),
            'hundred' => self::getHundredWords(),
            'ten' => self::getTenWords(),
            'ten10' => self::getTen10Words(),
            'unitary' => self::getUnitaryWords(),
        ];
    }

    /**
     * @param array<int, string> $parts
     * @return array<int, string>
     */
    private static function normalizeIntegerParts(array $parts): array
    {
        foreach ($parts as &$part) {
            $part = str_pad($part, self::PADDING_LENGTH, self::PADDING_CHAR, STR_PAD_LEFT);
        }
        return $parts;
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function convertHundred(string $value, array $words): string
    {
        $hundredValue = (int) $value[0];
        $isOnlyHundred = (int) $value > 100 && (int) $value < 200;

        return $isOnlyHundred ? 'cento' : $words['hundred'][$hundredValue];
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function convertTen(string $value, array $words): string
    {
        return (int) $value[1] < 2 ? '' : $words['ten'][(int) $value[1]];
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function convertUnit(string $value, array $words): string
    {
        if ((int) $value <= 0) {
            return '';
        }

        return (int) $value[1] === 1
            ? $words['ten10'][(int) $value[2]]
            : $words['unitary'][(int) $value[2]];
    }

    private static function buildExtensiveString(
        string $hundred,
        string $ten,
        string $unit
    ): string {
        $result = $hundred;

        if ($hundred && ($ten || $unit)) {
            $result .= ' e ';
        }

        $result .= $ten;

        if ($ten && $unit) {
            $result .= ' e ';
        }

        $result .= $unit;

        return $result;
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function getSingularOrPlural(string $value, int $position, array $words): string
    {
        return (int) $value > 1 ? $words['plural'][$position] : $words['singular'][$position];
    }

    private static function getSeparator(int $index, int $end): string
    {
        return $index < $end ? ', ' : ' e ';
    }

    private static function shouldAddSeparator(
        int $index,
        int $end,
        int $firstValue,
        int $zeroCounter
    ): bool {
        return $index > 0 && $index <= $end && $firstValue > 0 && $zeroCounter < 1;
    }

    /**
     * @param array<int, string> $integerParts
     */
    private static function calculateLastIndex(array $integerParts): int
    {
        $totalParts = count($integerParts);
        return $totalParts - ($integerParts[$totalParts - 1] > 0 ? 1 : 2);
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function addScaleToExtensiveString(
        string $extensiveString,
        string $part,
        int $position,
        array $words
    ): string {
        $scale = self::getSingularOrPlural($part, $position, $words);
        return $extensiveString . ' ' . $scale;
    }

    /**
     * @param array<int, string> $integerParts
     * @param array<string, array<int, string>> $words
     */
    private static function addSpecialThousandSuffix(
        string $extensiveString,
        int $position,
        int $zeroCounter,
        array $integerParts,
        array $words
    ): string {
        if ($position === 1 && $zeroCounter > 0 && (int) $integerParts[0] > 0) {
            return $extensiveString . ($zeroCounter > 1 ? ' de ' : ' ') . $words['plural'][$position];
        }
        return $extensiveString;
    }

    /**
     * @param array<int, string> $integerParts
     * @param array<string, array<int, string>> $words
     * @return array{extensiveString: string, zeroCounter: int}
     */
    private static function processIntegerPart(
        string $part,
        int $index,
        int $totalParts,
        int $zeroCounter,
        array $integerParts,
        array $words
    ): array {
        $hundred = self::convertHundred($part, $words);
        $ten = self::convertTen($part, $words);
        $unit = self::convertUnit($part, $words);
        $extensiveString = self::buildExtensiveString($hundred, $ten, $unit);

        if (!$extensiveString) {
            return ['extensiveString' => '', 'zeroCounter' => $part === '000' ? $zeroCounter + 1 : $zeroCounter,];
        }

        $position = $totalParts - 1 - $index;
        $extensiveString = self::addScaleToExtensiveString($extensiveString, $part, $position, $words);
        $extensiveString = self::addSpecialThousandSuffix(
            $extensiveString,
            $position,
            $zeroCounter,
            $integerParts,
            $words
        );

        $newZeroCounter = $zeroCounter > 0 ? $zeroCounter - 1 : 0;

        return ['extensiveString' => $extensiveString, 'zeroCounter' => $newZeroCounter,];
    }

    /**
     * @return array<int, string>
     */
    private static function formatValueForExtensive(float $value): array
    {
        $formattedValue = number_format(
            $value,
            self::DECIMAL_PLACES,
            self::DECIMAL_SEPARATOR,
            self::THOUSANDS_SEPARATOR
        );
        return self::normalizeIntegerParts(explode(self::DECIMAL_SEPARATOR, $formattedValue));
    }

    private static function throwInvalidArgumentException(string $message): never
    {
        throw new InvalidArgumentException($message);
    }

    private static function validateLength(string $nome, int $tamanho, string $value): void
    {
        if (strlen($value) !== $tamanho) {
            self::throwInvalidArgumentException("$nome precisa ter $tamanho números!");
        }
    }

    private static function validateNumeric(string $nome, string $value): void
    {
        if (!is_numeric($value)) {
            self::throwInvalidArgumentException("$nome precisa conter apenas números!");
        }
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

    protected static function validateForFormatting(string $nome, int $tamanho, string $value): void
    {
        self::validateLength($nome, $tamanho, $value);
        self::validateNumeric($nome, $value);
    }

    protected static function extensive(float $value = 0): string
    {
        if ($value == 0) {
            return 'zero';
        }

        $words = self::getExtensiveWordArrays();
        $integerParts = self::formatValueForExtensive($value);
        $totalParts = count($integerParts);
        $lastIndex = self::calculateLastIndex($integerParts);

        $result = '';
        $zeroCounter = 0;

        foreach ($integerParts as $index => $part) {
            $processed = self::processIntegerPart($part, $index, $totalParts, $zeroCounter, $integerParts, $words);
            $extensiveString = $processed['extensiveString'];
            $zeroCounter = $processed['zeroCounter'];

            if (!$extensiveString) {
                continue;
            }
            if (self::shouldAddSeparator($index, $lastIndex, (int) $integerParts[0], $zeroCounter)) {
                $result .= self::getSeparator($index, $lastIndex);
            }
            $result .= $extensiveString;
        }
        return $result ?: 'zero';
    }
}
