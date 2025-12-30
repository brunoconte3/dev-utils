<?php

namespace DevUtils;

class ValidatePhone
{
    public static function validate(string $phone): bool
    {
        $phone = intval(Format::onlyNumbers($phone));

        $phone = preg_replace('/\D+/', '', trim(strval($phone))) ?? '';
        $numberDigits = strlen($phone);

        if ($numberDigits < 10 || $numberDigits > 11) {
            return false;
        }
        if (preg_match('/^[1-9]{2}([0-9]{8}|[1-9]{1}[0-9]{8})$/', $phone)) {
            return true;
        }
        return false;
    }
}
