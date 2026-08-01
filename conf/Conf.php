<?php

declare(strict_types=1);

namespace DevUtils\conf;

final class Conf
{
    public function __construct()
    {
        $host = self::host();

        self::defineOnce('URL_HOST', $host);
        self::defineOnce('URL', $host . self::requestUri());
        self::defineOnce('PATH_PROJECT', self::pathProject());
    }

    private static function sanitizeUrl(mixed $value): string
    {
        return is_string($value) ? (string) filter_var($value, FILTER_SANITIZE_URL) : '';
    }

    private static function defineOnce(string $name, string $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public static function host(): string
    {
        return self::sanitizeUrl($_SERVER['HTTP_HOST'] ?? null);
    }

    public static function requestUri(): string
    {
        return self::sanitizeUrl($_SERVER['REQUEST_URI'] ?? null);
    }

    public static function pathProject(): string
    {
        return dirname(__DIR__);
    }
}
