<?php

declare(strict_types=1);

namespace DevUtils;

class Utility
{
    private static function buildCharset(
        bool $uppercase,
        bool $lowercase,
        bool $numbers,
        bool $symbols
    ): string {
        $charset = '';

        if ($numbers) {
            $charset .= str_shuffle('0123456789');
        }
        if ($symbols) {
            $charset .= str_shuffle('@#$!()-+%=');
        }
        if ($uppercase) {
            $charset .= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVYXWZ');
        }
        if ($lowercase) {
            $charset .= str_shuffle('abcdefghijklmnopqrstuvyxwz');
        }
        return $charset;
    }

    private static function isValidPassword(
        string $password,
        bool $uppercase,
        bool $lowercase,
        bool $numbers,
        bool $symbols
    ): bool {
        if ($uppercase && !preg_match('@[A-Z]@', $password)) {
            return false;
        }
        if ($lowercase && !preg_match('@[a-z]@', $password)) {
            return false;
        }
        if ($numbers && !preg_match('@[0-9]@', $password)) {
            return false;
        }
        if ($symbols && !preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        return true;
    }


    public static function captureClientIp(): mixed
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
        bool $symbols = true,
    ): string {
        $charset = self::buildCharset($uppercase, $lowercase, $numbers, $symbols);
        $password = substr(str_shuffle($charset), 0, $size);

        return self::isValidPassword($password, $uppercase, $lowercase, $numbers, $symbols)
            ? $password
            : self::generatePassword($size, $uppercase, $lowercase, $numbers, $symbols);
    }

    public static function buildUrl(string $host, string $absolutePath = '', ?string $https = ''): string
    {
        $protocol = ((isset($https) && ($https === 'on')) ? 'https' : 'http');
        return $protocol . '://' . $host . $absolutePath;
    }
}
