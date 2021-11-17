<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestInteger extends TestCase
{
    private function assembleArrayForTests(): array
    {
        return
            [
                'testIntError' => '0a',
                'testLeftZero' => '01',
                'testIntZero' => '0',
                'testIntZeroTyped' => 0,
                'testIntOne' => 1,
                'testIntNegative' => -2,
            ];
    }

    public function testInteger(): void
    {
        $array = $this->assembleArrayForTests();
        $rules = [
            'testIntError' => 'int',
            'testLeftZero' => 'int',
            'testIntZero' => 'int',
            'testIntZeroTyped' => 'int',
            'testIntOne' => 'int',
            'testIntNegative' => 'int',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(4, $validator->getErros());
    }

    public function testIntegerTyped(): void
    {
        $array = $this->assembleArrayForTests();
        $rules = [
            'testIntError' => 'integer',
            'testLeftZero' => 'integer',
            'testIntZero' => 'integer',
            'testIntZeroTyped' => 'integer',
            'testIntOne' => 'integer',
            'testIntNegative' => 'integer',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(3, $validator->getErros());
    }
}
