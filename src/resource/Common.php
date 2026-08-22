<?php

declare(strict_types=1);

namespace DevUtils\resource;

final class Common
{
    public static function searchLastLayerRecursive(
        array $arr,
        mixed $param,
        bool $found = false
    ): bool {
        foreach ($arr as $value) {
            if (is_array($value)) {
                $found = self::searchLastLayerRecursive($value, $param, $found);
            } else {
                if ((int) $value === (int) $param) {
                    $found = true;
                }
            }
            if ($found) {
                break;
            }
        }
        return $found;
    }
}
