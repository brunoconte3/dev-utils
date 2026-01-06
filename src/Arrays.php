<?php

declare(strict_types=1);

namespace DevUtils;

class Arrays
{
    private const XML_ATTR_KEY = '@attr';

    public static function searchKey(array $array, string $key): ?int
    {
        $position = array_search($key, array_keys($array), true);
        return $position !== false ? $position : null;
    }

    public static function renameKey(array &$array, string $oldKey, string $newKey): bool
    {
        if (!array_key_exists($oldKey, $array)) {
            return false;
        }

        $position = self::searchKey($array, $oldKey);
        if ($position === null) {
            return false;
        }

        $keys = array_keys($array);
        $keys[$position] = $newKey;
        $values = array_values($array);
        $array = array_combine($keys, $values);

        return true;
    }

    public static function checkExistIndexByValue(array $arrayCollection, string $search): bool
    {
        foreach ($arrayCollection as $item) {
            if (!is_array($item)) {
                if ($search === (string) $item) {
                    return true;
                }
                continue;
            }

            if (self::checkExistIndexByValue($item, $search)) {
                return true;
            }
        }
        return false;
    }

    public static function findValueByKey(array $array, string $searchKey): array
    {
        $result = [];
        $normalizedSearchKey = strtolower($searchKey);

        foreach ($array as $key => $value) {
            if (strtolower((string) $key) === $normalizedSearchKey) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $nestedResult = self::findValueByKey($value, $searchKey);
                if ($nestedResult !== []) {
                    $result[$key] = $nestedResult;
                }
            }
        }
        return $result;
    }

    public static function findIndexByValue(array $array, string | int | bool $searchValue): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if ($value === $searchValue) {
                    $result[$key] = $value;
                }
                continue;
            }

            $nestedResult = self::findIndexByValue($value, $searchValue);
            if ($nestedResult !== []) {
                $result[$key] = $nestedResult;
            }
        }
        return $result;
    }

    public static function convertArrayToXml(array $array, \SimpleXMLElement &$xml): void
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key) && is_array($value) && isset($value[self::XML_ATTR_KEY])) {
                $key = $value[self::XML_ATTR_KEY];
            }

            if (is_array($value)) {
                unset($value[self::XML_ATTR_KEY]);
                $subnode = $xml->addChild((string) $key);
                self::convertArrayToXml($value, $subnode);
                continue;
            }

            $xml->addChild((string) $key, htmlspecialchars((string) $value));
        }
    }

    public static function convertJsonIndexToArray(array &$array): void
    {
        array_walk_recursive($array, function (&$value) {
            if (is_string($value) && !empty($value)) {
                $arr = json_decode($value, true);
                if (is_array($arr) && (json_last_error() === JSON_ERROR_NONE)) {
                    $value = $arr;
                }
            }

            if (is_array($value)) {
                self::convertJsonIndexToArray($value);
            }
        });
    }

    public static function checkExistIndexArrayRecursive(?array $array, ?string $needle): bool
    {
        if ($array === null || $needle === null) {
            return false;
        }

        foreach ($array as $key => $value) {
            if ($key === $needle) {
                return true;
            }

            if (is_array($value) && self::checkExistIndexArrayRecursive($value, $needle)) {
                return true;
            }
        }

        return false;
    }
}
