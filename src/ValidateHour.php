<?php

declare(strict_types=1);

namespace DevUtils;

class ValidateHour
{
    public static function validateHour(string $hour): bool
    {
        return (bool) preg_match('/^(0\d|1\d|2[0-3]):([0-5]\d)$/', $hour);
    }
}
