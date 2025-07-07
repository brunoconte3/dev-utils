<?php

namespace DevUtils;

use DevUtils\DependencyInjection\TraitRuleCart;
use DevUtils\Format;

class ValidateCart
{
    use TraitRuleCart;

    protected const CARD_BRANDS = [
        'Elo' => '/^(401178|401179|431274|438935|451416|457393|457631|457632|504175|506699|5067|509|627780|636297|636368|650|651|655)/',
        'Visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
        'Mastercard' => '/^(5[1-5][0-9]{14}|2[2-7][0-9]{14})$/',
        'Hipercard' => '/^(606282\d{10}|3841\d{15}|637095\d{10,12}|637568\d{10,12}|637599\d{10,12}|637609\d{10,12}|637612\d{10,12})$/',
        'Amex' => '/^3[47][0-9]{13}$/',
    ];

    public static function onlyNumbers(string $number): string
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return $numberOnlyDigits;
    }

    public static function isVisa(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return self::ruleVisa($numberOnlyDigits);
    }

    public static function isMastercard(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return self::ruleMastercard($numberOnlyDigits);
    }

    public static function isElo(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return self::ruleElo($numberOnlyDigits);
    }

    public static function isHipercard(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return self::ruleHipercard($numberOnlyDigits);
    }

    public static function isAmex(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return self::ruleAmex($numberOnlyDigits);
    }

    public static function isValidCvv(string $cvv): bool
    {
        $cvvOnlyDigits = preg_replace('/\D/', '', $cvv);
        return self::ruleCvv($cvvOnlyDigits);
    }

    public static function luhn(string $number): bool
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
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
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        foreach (self::CARD_BRANDS as $brand => $pattern) {
            if (preg_match($pattern, $numberOnlyDigits)) {
                return $brand;
            }
        }
        return 'Desconhecida';
    }

    public static function maskCard(string $number, string $mask = '#### #### #### ####'): string
    {
        $number = self::onlyNumbers($number);
        return Format::mask($mask, $number);
    }

    public static function MaskCardHidden(string $number, int $start = 4, int $end = 4, string $char = '*'): string
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return Format::maskStringHidden($numberOnlyDigits, $start, $end, $char);
    }

    public static function MarkCardsecure(string $number): string
    {
        $numberOnlyDigits = preg_replace('/\D/', '', $number);
        return Format::maskStringHidden($numberOnlyDigits, 0, strlen($numberOnlyDigits) - 4, '*');
    }
} 