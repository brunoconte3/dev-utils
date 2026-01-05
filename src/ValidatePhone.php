<?php

namespace DevUtils;

class ValidatePhone
{
    public static function validate(string $phone): bool
    {
        $phone = Format::onlyNumbers($phone);
        $numberDigits = strlen($phone);

        if ($numberDigits < 10 || $numberDigits > 11) {
            return false;
        }

        return (bool) preg_match('/^[1-9]{2}([0-9]{8}|[1-9][0-9]{8})$/', $phone);
    }
}
