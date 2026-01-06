<?php

declare(strict_types=1);

namespace DevUtils;

class Uuid
{
    private static function formatUuid(string $hex): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    private static function setVersionAndVariant(string $hex, int $version): string
    {
        $timeHi = hexdec(substr($hex, 12, 4));
        $timeHi = ($timeHi & 0x0fff) | ($version << 12);

        $clockSeq = hexdec(substr($hex, 16, 4));
        $clockSeq = ($clockSeq & 0x3fff) | 0x8000;

        return substr($hex, 0, 12)
            . str_pad(dechex($timeHi), 4, '0', STR_PAD_LEFT)
            . str_pad(dechex($clockSeq), 4, '0', STR_PAD_LEFT)
            . substr($hex, 20);
    }

    private static function validateFormat(string $uuid): bool
    {
        return preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $uuid
        ) === 1;
    }

    private static function extractVersion(string $uuid): ?int
    {
        if (!self::validateFormat($uuid)) {
            return null;
        }

        $versionHex = substr($uuid, 14, 1);
        $version = (int) hexdec($versionHex);

        return ($version >= 1 && $version <= 8) ? $version : null;
    }

    public static function generate(): string
    {
        $timestamp = (int) (microtime(true) * 1000);
        $timestampHex = str_pad(dechex($timestamp), 12, '0', STR_PAD_LEFT);

        $randomBytes = random_bytes(10);
        $randomHex = bin2hex($randomBytes);

        $hex = $timestampHex . $randomHex;
        $hex = self::setVersionAndVariant($hex, 7);

        return self::formatUuid($hex);
    }

    public static function isValid(string $uuid, ?int $version = null): bool
    {
        $detectedVersion = self::extractVersion($uuid);

        if ($detectedVersion === null) {
            return false;
        }

        if ($version === null) {
            return in_array($detectedVersion, [1, 2, 3, 4, 5, 6, 7, 8], true);
        }

        return $detectedVersion === $version;
    }
}
