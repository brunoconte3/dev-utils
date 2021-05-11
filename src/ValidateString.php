<?php

namespace DevUtils;

class ValidateString
{
    public static function minWords(string $text, int $length): bool
    {
        return (count(explode(' ', $text)) >= $length);
    }

    public static function maxWords(string $text, int $length): bool
    {
        return (count(explode(' ', $text)) <= $length);
    }
}
