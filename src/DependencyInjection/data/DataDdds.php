<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection\data;

class DataDdds
{
    private const SOUTH_REGION = [
        'pr' => [41, 42, 43, 44, 45, 46],
        'rs' => [51, 53, 54, 55],
        'sc' => [47, 48, 49],
    ];

    private const SOUTHEAST_REGION = [
        'es' => [27, 28],
        'mg' => [31, 32, 33, 34, 35, 37, 38],
        'rj' => [21, 22, 24],
        'sp' => [11, 12, 13, 14, 15, 16, 17, 18, 19],
    ];

    private const MIDWEST_REGION = [
        'df' => [61],
        'go' => [62, 64],
        'ms' => [67],
        'mt' => [65, 66],
    ];

    private const NORTHEAST_REGION = [
        'al' => [82],
        'ba' => [71, 73, 74, 75, 77],
        'ce' => [85, 88],
        'ma' => [98, 99],
        'pb' => [83],
        'pe' => [87, 81],
        'pi' => [86, 89],
        'rn' => [84],
        'se' => [79],
    ];

    private const NORTH_REGION = [
        'ac' => [68],
        'am' => [92, 97],
        'ap' => [96],
        'pa' => [91, 93, 94],
        'ro' => [69],
        'rr' => [95],
        'to' => [63],
    ];

    public static function returnDddBrazil(): array
    {
        return [
            ...self::NORTH_REGION,
            ...self::NORTHEAST_REGION,
            ...self::MIDWEST_REGION,
            ...self::SOUTHEAST_REGION,
            ...self::SOUTH_REGION,
        ];
    }
}
