<?php

declare(strict_types=1);

namespace DevUtils\Test;

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
        return [
            'tratandoClasse' => 'convert|bool',
            'tratandoArray' => 'convert|bool',
            'tratandoInteiroPositivo' => 'convert|bool',
            'tratandoInteiroNegativo' => 'convert|bool',
            'tratandoStringTrue' => 'convert|bool',
            'tratandoStringOn' => 'convert|bool',
            'tratandoStringOff' => 'convert|bool',
            'tratandoStringYes' => 'convert|bool',
            'tratandoStringNo' => 'convert|bool',
            'tratandoStringUm' => 'convert|bool',
            'tratandoNull' => 'convert|bool',
            'tratandoInteiroZero' => 'convert|bool',
            'tratandoStringFalse' => 'convert|bool',
            'tratandoQualquerString' => 'convert|bool',
            'tratandoStringZero' => 'convert|bool',
            'tratandoStringVazio' => 'convert|bool',
        ];
    }
}
