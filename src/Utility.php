<?php

declare(strict_types=1);

namespace DevUtils;

use InvalidArgumentException;

class Utility
{
    private const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwxyz';
    private const NUMBER_CHARS = '0123456789';
    private const SYMBOL_CHARS = '@#$!()-+%=';
    private const SECURE_PROTOCOL_VALUES = ['on', '1', 'true', 'yes'];
    private const CLIENT_IP_SERVER_KEYS = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

    /**
     * @return array<int, non-empty-string>
     */
    private static function buildCharsetGroups(
        bool $uppercase,
        bool $lowercase,
        bool $numbers,
        bool $symbols
    ): array {
        $groups = [];

        if ($numbers) {
            $groups[] = self::NUMBER_CHARS;
        }
        if ($symbols) {
            $groups[] = self::SYMBOL_CHARS;
        }
        if ($uppercase) {
            $groups[] = self::UPPERCASE_CHARS;
        }
        if ($lowercase) {
            $groups[] = self::LOWERCASE_CHARS;
        }

        return $groups;
    }

    /**
     * @param non-empty-string $charset
     */
    private static function randomChar(string $charset): string
    {
        return $charset[random_int(0, strlen($charset) - 1)];
    }

    /**
     * @param array<int, string> $chars
     * @return array<int, string>
     */
    private static function secureShuffle(array $chars): array
    {
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return $chars;
    }

    public static function captureClientIp(): ?string
    {
        foreach (self::CLIENT_IP_SERVER_KEYS as $key) {
            $value = $_SERVER[$key] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public static function generatePassword(
        int $size,
        bool $uppercase = true,
        bool $lowercase = true,
        bool $numbers = true,
        bool $symbols = true,
    ): string {
        $groups = self::buildCharsetGroups($uppercase, $lowercase, $numbers, $symbols);

        if ($groups === []) {
            throw new InvalidArgumentException('Ao menos um conjunto de caracteres deve ser habilitado!');
        }

        if ($size < count($groups)) {
            throw new InvalidArgumentException(
                'O tamanho da senha deve ser no mínimo ' . count($groups) . ' para os conjuntos habilitados!'
            );
        }

        $charset = implode('', $groups);
        $chars = [];

        foreach ($groups as $group) {
            $chars[] = self::randomChar($group);
        }

        while (count($chars) < $size) {
            $chars[] = self::randomChar($charset);
        }

        return implode('', self::secureShuffle($chars));
    }

    public static function buildUrl(string $host, string $absolutePath = '', ?string $https = null): string
    {
        $protocol = in_array(strtolower($https ?? ''), self::SECURE_PROTOCOL_VALUES, true) ? 'https' : 'http';

        return sprintf('%s://%s%s', $protocol, $host, $absolutePath);
    }
}
