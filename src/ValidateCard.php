<?php

namespace DevUtils;

use DevUtils\DependencyInjection\TraitRuleCard;
use DevUtils\Format;

class ValidateCard
{
    use TraitRuleCard;

    protected const CARD_BRANDS = [
        'Elo' => '/^(401178|401179|431274|438935|451416|457393|457631|457632|504175|506699|5067|509|627780|636297|636368|650|651|655)/',
        'Visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
        'Mastercard' => '/^(5[1-5][0-9]{14}|2[2-7][0-9]{14})$/',
        'Hipercard' => '/^(606282\d{10}|3841\d{15}|637095\d{10,12}|637568\d{10,12}|637599\d{10,12}|637609\d{10,12}|637612\d{10,12})$/',
        'Amex' => '/^3[47][0-9]{13}$/',
    ];

    public static function onlyNumbers(string $number): string
    {
        return Format::onlyNumbers($number);
    }

    public static function isVisa(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return self::ruleVisa($numberOnlyDigits);
    }

    public static function isMastercard(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return self::ruleMastercard($numberOnlyDigits);
    }

    public static function isElo(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return self::ruleElo($numberOnlyDigits);
    }

    public static function isHipercard(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return self::ruleHipercard($numberOnlyDigits);
    }

    public static function isAmex(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return self::ruleAmex($numberOnlyDigits);
    }

    public static function isValidCvv(string $cvv, ?string $brand = null): bool
    {
        $cvvOnlyDigits = Format::onlyNumbers($cvv);
        
        // If brand is specified, validate according to it
        if ($brand !== null) {
            if ($brand === 'Amex') {
                return (bool)preg_match('/^\d{4}$/', $cvvOnlyDigits);
            } else {
                return (bool)preg_match('/^\d{3}$/', $cvvOnlyDigits);
            }
        }
        
        // Default validation: accepts 3 or 4 digits (for compatibility)
        return (bool)preg_match('/^\d{3,4}$/', $cvvOnlyDigits);
    }

    public static function luhn(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        $sum = 0;
        $alt = false;
        for ($i = strlen($numberOnlyDigits) - 1; $i >= 0; $i--) {
            $n = intval($numberOnlyDigits[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = !$alt;
        }
        return ($sum % 10 == 0);
    }

    public static function getBrand(string $number): string
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        foreach (self::CARD_BRANDS as $brand => $pattern) {
            if (preg_match($pattern, $numberOnlyDigits)) {
                return $brand;
            }
        }
        return 'Desconhecida';
    }
} 