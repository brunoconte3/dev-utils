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


    public static function captureClientIp(): ?string
    {
        $clientIp = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        if (!empty($clientIp)) {
            return $clientIp;
        }

        $forwardedFor = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        if (!empty($forwardedFor)) {
            return $forwardedFor;
        }

        $remoteAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        return $remoteAddr ?: null;
    }

    public static function generatePassword(
        int $size,
        bool $uppercase = true,
        bool $lowercase = true,
        bool $numbers = true,
        bool $symbols = true,
    ): string {
        $charset = self::buildCharset($uppercase, $lowercase, $numbers, $symbols);
        $maxAttempts = 100;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $password = substr(str_shuffle($charset), 0, $size);

            if (self::isValidPassword($password, $uppercase, $lowercase, $numbers, $symbols)) {
                return $password;
            }
        }

        return substr(str_shuffle($charset), 0, $size);
    }

    public static function buildUrl(string $host, string $absolutePath = '', ?string $https = null): string
    {
        $protocol = ($https === 'on') ? 'https' : 'http';
        return sprintf('%s://%s%s', $protocol, $host, $absolutePath);
    }
}
