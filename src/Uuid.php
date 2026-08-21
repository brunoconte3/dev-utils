<?php

declare(strict_types=1);

namespace DevUtils;

class Uuid
{
    private const VERSION = 7;
    private const REGEX_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-([1-8])[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i';

    private static function formatUuid(string $hex): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }

    private static function applyVersionAndVariant(string $bytes): string
    {
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | (self::VERSION << 4));
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return $bytes;
    }

    private static function extractVersion(string $uuid): ?int
    {
        if (preg_match(self::REGEX_UUID, $uuid, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    public static function generate(): string
    {
        $timestampMs = (int) (microtime(true) * 1000);
        $timestampBytes = substr(pack('J', $timestampMs), -6);
        $bytes = self::applyVersionAndVariant($timestampBytes . random_bytes(10));

        return self::formatUuid(bin2hex($bytes));
    }

    public static function isValid(string $uuid, ?int $version = null): bool
    {
        $detectedVersion = self::extractVersion($uuid);

        if ($detectedVersion === null) {
            return false;
        }

        return $version === null || $detectedVersion === $version;
    }
}
