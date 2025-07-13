<?php

namespace DevUtils\DependencyInjection;

use DevUtils\Format;

trait TraitRuleCard
{
    protected static function ruleVisa(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return (bool)preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $numberOnlyDigits);
    }

    protected static function ruleMastercard(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return (bool)preg_match('/^(5[1-5][0-9]{14}|2[2-7][0-9]{14})$/', $numberOnlyDigits);
    }

    protected static function ruleElo(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        $eloBins = [
            '401178', '401179', '431274', '438935', '451416', '457393', '457631', '457632',
            '504175', '506699', '5067', '509', '627780', '636297', '636368', '650', '651', '655'
        ];
        foreach ($eloBins as $bin) {
            if (strpos($numberOnlyDigits, $bin) === 0) {
                return true;
            }
        }
        return false;
    }

    protected static function ruleHipercard(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return (bool)preg_match('/^(606282\d{10}|3841\d{15}|637095\d{10,12}|637568\d{10,12}|637599\d{10,12}|637609\d{10,12}|637612\d{10,12})$/', $numberOnlyDigits);
    }

    protected static function ruleAmex(string $number): bool
    {
        $numberOnlyDigits = Format::onlyNumbers($number);
        return (bool)preg_match('/^3[47][0-9]{13}$/', $numberOnlyDigits);
    }

    protected static function ruleCvv(string $cvv): bool
    {
        $cvvOnlyDigits = Format::onlyNumbers($cvv);
        // CVV must have exactly 3 digits for most cards
        // or 4 digits for American Express
        return (bool)preg_match('/^\d{3}$/', $cvvOnlyDigits);
    }
} 