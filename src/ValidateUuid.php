<?php

declare(strict_types=1);

namespace DevUtils;

class ValidateUuid
{
    private const UUID_V4_V7_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[47][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    private static function matchesUuidV4OrV7(string $uuid): bool
    {
        return preg_match(self::UUID_V4_V7_REGEX, $uuid) === 1;
    }

    public static function isValid(string $uuid): bool
    {
        if (empty($uuid)) {
            return false;
        }

        return self::matchesUuidV4OrV7($uuid);
    }
}
