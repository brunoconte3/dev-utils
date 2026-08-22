<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    private const RULE_NUM_MIN_10 = 'numMin:10';
    private const RULE_NUM_MAX_100 = 'numMax:100';

    private function assembleArrayForTests(): array
    {
        return [
            'testIntError' => '0a',
            'testIntNegative' => -2,
            'testIntOne' => 1,
            'testIntZero' => '0',
            'testIntZeroTyped' => 0,
            'testLeftZero' => '01',
        ];
    }

    public function testInteger(): void
    {
        $array = $this->assembleArrayForTests();
        $rules = [
            'testIntError' => 'int',
            'testIntNegative' => 'int',
            'testIntOne' => 'int',
            'testIntZero' => 'int',
            'testIntZeroTyped' => 'int',
            'testLeftZero' => 'int',
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
            'testIntNegative' => 'integer',
            'testIntOne' => 'integer',
            'testIntZero' => 'integer',
            'testIntZeroTyped' => 'integer',
            'testLeftZero' => 'integer',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(3, $validator->getErros());
    }

    public function testIntegerWithCustomMessage(): void
    {
        $msg = 'Mensagem customizada, campo inválido';
        $array = ['field' => 'abc'];
        $rules = ['field' => 'int, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertArrayHasKey('field', $validator->getErros());
        self::assertEquals($msg, $validator->getErros()['field']);
    }

    public function testIntegerValid(): void
    {
        $array = ['number' => '123', 'negative' => '-456'];
        $rules = ['number' => 'int', 'negative' => 'int'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumeric(): void
    {
        $array = [
            'float' => '123.45',
            'integer' => '123',
            'invalid' => 'abc',
            'negative' => '-100',
        ];
        $rules = [
            'float' => 'numeric',
            'integer' => 'numeric',
            'invalid' => 'numeric',
            'negative' => 'numeric',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('invalid', $validator->getErros());
    }

    public function testNumericWithCustomMessage(): void
    {
        $msg = 'Deve ser numérico, campo inválido';
        $array = ['field' => 'text'];
        $rules = ['field' => 'numeric, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['field']);
    }

    public function testNumMax(): void
    {
        $array = ['exceeded' => '150'];
        $rules = ['exceeded' => self::RULE_NUM_MAX_100];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('exceeded', $validator->getErros());
    }

    public function testNumMaxValid(): void
    {
        $array = ['valid' => '50', 'maximum' => '100',];
        $rules = ['valid' => self::RULE_NUM_MAX_100, 'maximum' => self::RULE_NUM_MAX_100];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMaxNegativeValue(): void
    {
        $array = ['negative' => '-5'];
        $rules = ['negative' => self::RULE_NUM_MAX_100];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMaxWithCustomMessage(): void
    {
        $msg = 'Valor máximo excedido, tente novamente';
        $array = ['field' => '200'];
        $rules = ['field' => 'numMax:100, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['field']);
    }

    public function testNumMin(): void
    {
        $array = [
            'below' => '5',
            'minimum' => '10',
            'valid' => '50',
        ];
        $rules = [
            'below' => self::RULE_NUM_MIN_10,
            'minimum' => self::RULE_NUM_MIN_10,
            'valid' => self::RULE_NUM_MIN_10,
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('below', $validator->getErros());
    }

    public function testNumMinNegativeValue(): void
    {
        $array = ['negative' => '-5'];
        $rules = ['negative' => 'numMin:0'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMinNotNumeric(): void
    {
        $array = ['text' => 'abc'];
        $rules = ['text' => self::RULE_NUM_MIN_10];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMonth(): void
    {
        $array = [
            'withZero' => '01',
            'december' => '12',
            'january' => '1',
        ];
        $rules = [
            'withZero' => 'numMonth',
            'december' => 'numMonth',
            'january' => 'numMonth',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMonthInvalid(): void
    {
        $array = [
            'text' => 'jan',
            'threeDigits' => '123',
            'thirteen' => '13',
            'zero' => '0',
        ];
        $rules = [
            'text' => 'numMonth',
            'threeDigits' => 'numMonth',
            'thirteen' => 'numMonth',
            'zero' => 'numMonth',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(4, $validator->getErros());
    }

    public function testNumMonthWithCustomMessage(): void
    {
        $msg = 'Mês inválido, informe um valor entre 1 e 12';
        $array = ['month' => '15'];
        $rules = ['month' => 'numMonth, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['month']);
    }

    public function testIntegerTypedWithCustomMessage(): void
    {
        $msg = 'Deve ser inteiro tipado, não string';
        $array = ['field' => '123'];
        $rules = ['field' => 'integer, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['field']);
    }
}
