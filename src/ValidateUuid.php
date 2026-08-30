<?php

declare(strict_types=1);

namespace DevUtils;

class ValidateUuid
{
    private const ACCEPTED_VERSIONS = [4, 7];

    public static function isValid(string $uuid): bool
    {
        foreach (self::ACCEPTED_VERSIONS as $version) {
            if (Uuid::isValid($uuid, $version)) {
                return true;
            }
        }

        return false;
    }
}
