<?php

namespace DevUtils;

class ValidateHour
{
    public static function validateHour(string $hour): bool
    {
        return (bool) preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $hour);
    }
}
