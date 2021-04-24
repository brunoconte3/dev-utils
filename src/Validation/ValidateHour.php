<?php

namespace devUtils\Validation;

class ValidateHour
{
    public static function validateHour(string $campo): bool
    {
        return preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])/', $campo) ? $campo : false;
    }
}
