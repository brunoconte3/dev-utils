<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection\data;

use stdClass;

class DataConvertTypesBool
{
    public function arrayData(): array
    {
        return [
            'tratandoArray' => [1, 2], //true
            'tratandoClasse' => new stdClass(), //true
            'tratandoInteiroNegativo' => -42, //true
            'tratandoInteiroPositivo' => 42, //true
            'tratandoInteiroZero' => 0, // false
            'tratandoNull' => null, // false
            'tratandoQualquerString' => 'string', //false
            'tratandoStringFalse' => 'false', //false
            'tratandoStringNo' => 'no', //false
            'tratandoStringOff' => 'off', //true
            'tratandoStringOn' => 'on', //true
            'tratandoStringTrue' => 'true', //true
            'tratandoStringUm' => '1', // true
            'tratandoStringVazio' => '', // false
            'tratandoStringYes' => 'yes', //true
            'tratandoStringZero' => '0', // false
        ];
    }

    public function arrayRule(): array
    {
        return array_fill_keys(array_keys($this->arrayData()), 'convert|bool');
    }
}
