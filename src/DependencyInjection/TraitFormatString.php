<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

trait TraitFormatString
{
    public static function ucwordsCharset(string $string, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($string, $charset), MB_CASE_TITLE, 'UTF-8');
    }

    public static function mask(string $mask, string $value): string
    {
        $cleanValue = str_replace(' ', '', $value);

        for ($i = 0; $i < strlen($cleanValue); $i++) {
            $position = strpos($mask, "#");
            if ($position === false) {
                continue;
            }

            $mask[$position] = $cleanValue[$i];
        }

        return $mask;
    }

    public static function maskStringHidden(
        string $string,
        int $qtdHidden,
        int $positionHidden,
        string $char,
    ): ?string {
        if (empty(trim($string))) {
            return null;
        }
        if ($qtdHidden > strlen($string)) {
            throw new
                InvalidArgumentException('Quantidade de caracteres para ocultar não pode ser maior que a String!');
        }
        if ($qtdHidden < 1) {
            throw new InvalidArgumentException('Quantidade de caracteres para ocultar não pode ser menor que 1!');
        }
        $chars = str_repeat($char, $qtdHidden);
        if ($positionHidden < 0 || $positionHidden + strlen($chars) > strlen($string)) {
            throw new InvalidArgumentException('Posição para ocultar está fora do intervalo da String!');
        }
        return substr_replace($string, $chars, $positionHidden, strlen($chars));
    }

    public static function reverse(string $string, string $charSet = 'UTF-8'): string
    {
        if (!extension_loaded('iconv')) {
            throw new InvalidArgumentException(__METHOD__ . '() requires ICONV extension that is not loaded.');
        }
        return iconv('UTF-32LE', $charSet, strrev(iconv($charSet, 'UTF-32BE', $string) ?: '')) ?: '';
    }

    public static function removeAccent(?string $string): ?string
    {
        if ($string === null || $string === '') {
            return null;
        }
        return preg_replace(
            [
                '/(á|à|ã|â|ä)/',
                '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/',
                '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/',
                '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/',
                '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/',
                '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/',
                '/(Ñ)/',
                '/(ç)/',
                '/(Ç)/',
            ],
            explode(' ', 'a A e E i I o O u U n N c C'),
            $string,
        );
    }

    public static function removeSpecialCharacters(string $string, bool $space = true): ?string
    {
        if ($string === '') {
            return null;
        }
        $newString = self::removeAccent($string) ?? '';
        if ($space) {
            return preg_replace("/[^a-zA-Z0-9 ]/", "", $newString);
        }
        return preg_replace("/[^a-zA-Z0-9]/", "", $newString);
    }

    public static function upper(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtoupper($string, $charset);
    }

    public static function lower(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtolower($string, $charset);
    }

    public static function onlyNumbers(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }

    public static function onlyLettersNumbers(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $value) ?? '';
    }

    public static function slugfy(string $text): string
    {
        $noSpecialCharacter = self::removeSpecialCharacters(str_replace('-', ' ', $text)) ?? '';
        $slug = preg_replace('/\s+/', '-', trim($noSpecialCharacter)) ?? '';

        return self::lower($slug);
    }

    public static function convertStringToBinary(string $string): string
    {
        $characters = str_split($string);
        $binaryParts = [];

        foreach ($characters as $character) {
            $hexData = unpack('H*', $character) ?: [];
            if (!isset($hexData[1]) || !is_string($hexData[1])) {
                continue;
            }

            $binaryParts[] = base_convert($hexData[1], 16, 2);
        }

        return implode(' ', $binaryParts);
    }
}
