<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitExtensiveWords
{
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
            'hundred' => self::getHundredWords(),
            'plural' => self::getPluralScaleWords(),
            'singular' => self::getSingularScaleWords(),
            'ten' => self::getTenWords(),
            'ten10' => self::getTen10Words(),
            'unitary' => self::getUnitaryWords(),
        ];
    }
}
