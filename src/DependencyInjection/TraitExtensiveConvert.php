<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitExtensiveConvert
{
    private const DECIMAL_PLACES = 2;
    private const GROUP_SIZE = 3;
    private const SCALE_CENTS = 0;
    private const SCALE_UNIT = 1;
    private const SCALE_THOUSAND = 2;
    private const SCALE_MILLION = 3;

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
                $text = $scale === self::SCALE_THOUSAND && $number === 1
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
}
