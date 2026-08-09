<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

abstract class FormatAux
{
    private const DATA_TYPE_TO_CONVERT = ['bool', 'float', 'int', 'numeric',];
    private const DECIMAL_PLACES = 2;
    private const GROUP_SIZE = 3;
    private const SCALE_CENTS = 0;
    private const SCALE_UNIT = 1;
    private const SCALE_THOUSAND = 2;
    private const SCALE_MILLION = 3;
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
        return ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove',];
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
    private static function convertGroup(string $group, array $words): string
    {
        return self::buildExtensiveString(
            self::convertHundred($group, $words),
            self::convertTen($group, $words),
            self::convertUnit($group, $words),
        );
    }

    /**
     * @return array{0: string, 1: int}
     */
    private static function splitCurrency(float $value): array
    {
        [$integer, $cents] = explode('.', number_format($value, self::DECIMAL_PLACES, '.', ''));

        return [$integer, (int) $cents];
    }

    /**
     * @return array<int, string>
     */
    private static function splitIntoGroups(string $digits): array
    {
        $length = (int) (ceil(strlen($digits) / self::GROUP_SIZE) * self::GROUP_SIZE);

        return str_split(str_pad($digits, $length, '0', STR_PAD_LEFT), self::GROUP_SIZE);
    }

    /**
     * @param array<string, array<int, string>> $words
     * @return array<int, array{text: string, number: int, scale: int}>
     */
    private static function buildIntegerChunks(string $digits, array $words): array
    {
        $groups = self::splitIntoGroups($digits);
        $totalGroups = count($groups);
        $chunks = [];

        foreach ($groups as $index => $group) {
            $number = (int) $group;
            if ($number === 0) {
                continue;
            }

            $scale = $totalGroups - $index;
            $text = self::convertGroup($group, $words);

            if ($scale >= self::SCALE_THOUSAND) {
                $scaleWord = $number > 1 ? $words['plural'][$scale] : $words['singular'][$scale];
                $text = ($scale === self::SCALE_THOUSAND && $number === 1)
                    ? $scaleWord
                    : $text . ' ' . $scaleWord;
            }

            $chunks[] = ['text' => $text, 'number' => $number, 'scale' => $scale];
        }

        return $chunks;
    }

    /**
     * @param array<int, array{text: string, number: int, scale: int}> $chunks
     */
    private static function joinChunks(array $chunks): string
    {
        $texts = array_column($chunks, 'text');
        if (count($texts) < 2) {
            return implode('', $texts);
        }

        $lastChunk = $chunks[count($chunks) - 1];
        $lastText = (string) array_pop($texts);
        $useAnd = $lastChunk['number'] < 100 || $lastChunk['number'] % 100 === 0;

        return implode(', ', $texts) . ($useAnd ? ' e ' : ', ') . $lastText;
    }

    /**
     * @param array<int, array{text: string, number: int, scale: int}> $chunks
     * @param array<string, array<int, string>> $words
     */
    private static function currencySuffix(array $chunks, array $words): string
    {
        $lastChunk = $chunks[count($chunks) - 1];
        $isSingular = $lastChunk['scale'] === self::SCALE_UNIT && $lastChunk['number'] === 1;
        $currency = $isSingular
            ? $words['singular'][self::SCALE_UNIT]
            : $words['plural'][self::SCALE_UNIT];

        return ($lastChunk['scale'] >= self::SCALE_MILLION ? ' de ' : ' ') . $currency;
    }

    /**
     * @param array<string, array<int, string>> $words
     */
    private static function centsToWords(int $cents, array $words): string
    {
        $group = str_pad((string) $cents, self::GROUP_SIZE, '0', STR_PAD_LEFT);
        $unit = $cents > 1 ? $words['plural'][self::SCALE_CENTS] : $words['singular'][self::SCALE_CENTS];

        return self::convertGroup($group, $words) . ' ' . $unit;
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
