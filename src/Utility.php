<?php

declare(strict_types=1);

namespace DevUtils;

class Utility
{
    public static function captureClientIp(): ?string
    {
        if (!empty(filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP'))) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty(filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'))) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else {
            $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }
        return $ip;
    }

    public static function generatePassword(
        int $size,
        bool $uppercase = true,
        bool $lowercase = true,
        bool $numbers = true,
        bool $symbols = true
    ): string {
        $alphabet = 'abcdefghijklmnopqrstuvyxwz';
        $nums = '0123456789';
        $sym = '@#$!()-+%=';
        $password = null;

        if ($numbers) {
            $password .= str_shuffle($nums);
        }
        if ($symbols) {
            $password .= str_shuffle($sym);
        }
        if ($uppercase) {
            $password .= str_shuffle(strtoupper($alphabet));
        }
        if ($lowercase) {
            $password .= str_shuffle(strtolower($alphabet));
        }
        return substr(str_shuffle($password), 0, $size);
    }
}
