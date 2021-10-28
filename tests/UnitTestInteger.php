<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestInteger extends TestCase
{
    public function testInteger(): void
    {
        $array = [
            'testNumberError' => '01',
            'testIntError' => '0a',
            'testIntZeroValid' => 0,
            'testIntOneValid' => 1,
        ];
        $rules = [
            'testNumberError' => 'int',
            'testIntError' => 'int',
            'testIntZeroValid' => 'int',
            'testIntOneValid' => 'int',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }
}
