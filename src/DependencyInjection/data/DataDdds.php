<?php

namespace DevUtils\DependencyInjection\data;

class DataDdds
{
    private static function returnSouthRegion(): array
    {
        return [
            'pr' => [41, 42, 43, 44, 45, 46,],
            'rs' => [51, 53, 54, 55,],
            'sc' => [47, 48, 49,],
        ];
    }

    private static function returnSoutheastRegion(): array
    {
        return [
            'rj' => [21, 22, 24,],
            'sp' => [11, 12, 13, 14, 15, 16, 17, 18, 19,],
            'es' => [27, 28,],
            'mg' => [31, 32, 33, 34, 35, 37, 38,],
            'df' => [61,],
        ];
    }

    private static function returnMidwestRegion(): array
    {
        return [
            'go' => [62, 64,],
            'mg' => [31, 32, 33, 34, 35, 37, 38],
            'ms' => [67,],
            'mt' => [65, 66],
        ];
    }

    private static function returnNortheastRegion(): array
    {
        return [
            'ma' => [98, 99,],
            'al' => [82,],
            'ba' => [71, 73, 74, 75, 77,],
            'ce' => [85, 88,],
            'pb' => [83,],
            'pe' => [87, 81,],
            'pi' => [86, 89,],
            'rn' => [84],
            'se' => [79],
        ];
    }

    private static function returnNorthRegion(): array
    {
        return [
            'ac' => [68],
            'ap' => [96],
            'am' => [92, 97,],
            'pa' => [91, 93, 94,],
            'ro' => [69,],
            'rr' => [95,],
            'to' => [63,],
        ];
    }

    public static function returnDddBrazil(): array
    {
        $values = self::returnNorthRegion();
        $values += self::returnNortheastRegion();
        $values += self::returnMidwestRegion();
        $values += self::returnSoutheastRegion();
        $values += self::returnSouthRegion();
        return $values;
    }
}
