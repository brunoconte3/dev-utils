<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection\data;

use stdClass;

class DataConvertTypesBool
{
    public function arrayData(): array
    {
        return [
            'handlingAnyString' => 'string', //false
            'handlingArray' => [1, 2], //true
            'handlingClass' => new stdClass(), //true
            'handlingEmptyString' => '', // false
            'handlingNegativeInteger' => -42, //true
            'handlingNull' => null, // false
            'handlingPositiveInteger' => 42, //true
            'handlingStringFalse' => 'false', //false
            'handlingStringNo' => 'no', //false
            'handlingStringOff' => 'off', //true
            'handlingStringOn' => 'on', //true
            'handlingStringOne' => '1', // true
            'handlingStringTrue' => 'true', //true
            'handlingStringYes' => 'yes', //true
            'handlingStringZero' => '0', // false
            'handlingZeroInteger' => 0, // false
        ];
    }

    public function arrayRule(): array
    {
        return array_fill_keys(array_keys($this->arrayData()), 'convert|bool');
    }
}
