<?php

namespace DevUtils;

class ValidateString
{
    private static function countWords(string $text): int
    {
        $text = trim($text);
        if (empty($text)) {
            return 0;
        }

        $words = preg_split('/\s+/', $text);
        return is_array($words) ? count($words) : 0;
    }

    public static function minWords(string $text, int $length): bool
    {
        return self::countWords($text) >= $length;
    }

    public static function maxWords(string $text, int $length): bool
    {
        return self::countWords($text) <= $length;
    }
}
