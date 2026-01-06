<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection\data;

use stdClass;

class DataConvertTypesBool
{
    public function arrayData(): array
    {
        return [
            'tratandoClasse' => new stdClass(), //true
            'tratandoArray' => [1, 2], //true
            'tratandoInteiroPositivo' => 42, //true
            'tratandoInteiroNegativo' => -42, //true
            'tratandoStringTrue' => 'true', //true
            'tratandoStringOn' => 'on', //true
            'tratandoStringOff' => 'off', //true
            'tratandoStringYes' => 'yes', //true
            'tratandoStringNo' => 'no', //false
            'tratandoStringUm' => '1', // true
            'tratandoNull' => null, // false
            'tratandoInteiroZero' => 0, // false
            'tratandoStringFalse' => 'false', //false
            'tratandoQualquerString' => 'string', //false
            'tratandoStringZero' => '0', // false
            'tratandoStringVazio' => '', // false
        ];
    }

    public function arrayRule(): array
    {
        return array_fill_keys(array_keys($this->arrayData()), 'convert|bool');
    }
}
