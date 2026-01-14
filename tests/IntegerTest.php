<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    private function assembleArrayForTests(): array
    {
        return [
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

    public function testIntegerWithCustomMessage(): void
    {
        $msg = 'Mensagem customizada, campo inválido';
        $array = ['campo' => 'abc'];
        $rules = ['campo' => 'int, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertArrayHasKey('campo', $validator->getErros());
        self::assertEquals($msg, $validator->getErros()['campo']);
    }

    public function testIntegerValid(): void
    {
        $array = ['numero' => '123', 'negativo' => '-456'];
        $rules = ['numero' => 'int', 'negativo' => 'int'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumeric(): void
    {
        $array = [
            'inteiro' => '123',
            'float' => '123.45',
            'negativo' => '-100',
            'invalido' => 'abc',
        ];
        $rules = [
            'inteiro' => 'numeric',
            'float' => 'numeric',
            'negativo' => 'numeric',
            'invalido' => 'numeric',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('invalido', $validator->getErros());
    }

    public function testNumericWithCustomMessage(): void
    {
        $msg = 'Deve ser numérico, campo inválido';
        $array = ['campo' => 'texto'];
        $rules = ['campo' => 'numeric, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['campo']);
    }

    public function testNumMax(): void
    {
        $array = ['excedido' => '150'];
        $rules = ['excedido' => 'numMax:100'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('excedido', $validator->getErros());
    }

    public function testNumMaxValid(): void
    {
        $array = ['valido' => '50', 'maximo' => '100',];
        $rules = ['valido' => 'numMax:100', 'maximo' => 'numMax:100'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMaxNegativeValue(): void
    {
        $array = ['negativo' => '-5'];
        $rules = ['negativo' => 'numMax:100'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMaxWithCustomMessage(): void
    {
        $msg = 'Valor máximo excedido, tente novamente';
        $array = ['campo' => '200'];
        $rules = ['campo' => 'numMax:100, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['campo']);
    }

    public function testNumMin(): void
    {
        $array = [
            'valido' => '50',
            'minimo' => '10',
            'abaixo' => '5',
        ];
        $rules = [
            'valido' => 'numMin:10',
            'minimo' => 'numMin:10',
            'abaixo' => 'numMin:10',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('abaixo', $validator->getErros());
    }

    public function testNumMinNegativeValue(): void
    {
        $array = ['negativo' => '-5'];
        $rules = ['negativo' => 'numMin:0'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMinNotNumeric(): void
    {
        $array = ['texto' => 'abc'];
        $rules = ['texto' => 'numMin:10'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMonth(): void
    {
        $array = [
            'janeiro' => '1',
            'dezembro' => '12',
            'comZero' => '01',
        ];
        $rules = [
            'janeiro' => 'numMonth',
            'dezembro' => 'numMonth',
            'comZero' => 'numMonth',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMonthInvalid(): void
    {
        $array = [
            'zero' => '0',
            'treze' => '13',
            'texto' => 'jan',
            'tresDigitos' => '123',
        ];
        $rules = [
            'zero' => 'numMonth',
            'treze' => 'numMonth',
            'texto' => 'numMonth',
            'tresDigitos' => 'numMonth',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(4, $validator->getErros());
    }

    public function testNumMonthWithCustomMessage(): void
    {
        $msg = 'Mês inválido, informe um valor entre 1 e 12';
        $array = ['mes' => '15'];
        $rules = ['mes' => 'numMonth, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['mes']);
    }

    public function testIntegerTypedWithCustomMessage(): void
    {
        $msg = 'Deve ser inteiro tipado, não string';
        $array = ['campo' => '123'];
        $rules = ['campo' => 'integer, ' . $msg];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertEquals($msg, $validator->getErros()['campo']);
    }
}
