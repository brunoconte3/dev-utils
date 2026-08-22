<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection\data;

use stdClass;

class DataConvertTypesBool
{
    public function arrayData(): array
    {
        return [
            'handlingArray' => [1, 2], //true
            'handlingClass' => new stdClass(), //true
            'handlingNegativeInteger' => -42, //true
            'handlingPositiveInteger' => 42, //true
            'handlingZeroInteger' => 0, // false
            'handlingNull' => null, // false
            'handlingAnyString' => 'string', //false
            'handlingStringFalse' => 'false', //false
            'handlingStringNo' => 'no', //false
            'handlingStringOff' => 'off', //true
            'handlingStringOn' => 'on', //true
            'handlingStringTrue' => 'true', //true
            'handlingStringOne' => '1', // true
            'handlingEmptyString' => '', // false
            'handlingStringYes' => 'yes', //true
            'handlingStringZero' => '0', // false
        ];
    }

    public function arrayRule(): array
    {
        return array_fill_keys(array_keys($this->arrayData()), 'convert|bool');
    }
}
