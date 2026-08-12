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
            'float' => '123.45',
            'inteiro' => '123',
            'invalido' => 'abc',
            'negativo' => '-100',
        ];
        $rules = [
            'float' => 'numeric',
            'inteiro' => 'numeric',
            'invalido' => 'numeric',
            'negativo' => 'numeric',
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
        $rules = ['excedido' => self::RULE_NUM_MAX_100];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertArrayHasKey('excedido', $validator->getErros());
    }

    public function testNumMaxValid(): void
    {
        $array = ['valido' => '50', 'maximo' => '100',];
        $rules = ['valido' => self::RULE_NUM_MAX_100, 'maximo' => self::RULE_NUM_MAX_100];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMaxNegativeValue(): void
    {
        $array = ['negativo' => '-5'];
        $rules = ['negativo' => self::RULE_NUM_MAX_100];
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
            'abaixo' => '5',
            'minimo' => '10',
            'valido' => '50',
        ];
        $rules = [
            'abaixo' => self::RULE_NUM_MIN_10,
            'minimo' => self::RULE_NUM_MIN_10,
            'valido' => self::RULE_NUM_MIN_10,
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
        $rules = ['texto' => self::RULE_NUM_MIN_10];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMonth(): void
    {
        $array = [
            'comZero' => '01',
            'dezembro' => '12',
            'janeiro' => '1',
        ];
        $rules = [
            'comZero' => 'numMonth',
            'dezembro' => 'numMonth',
            'janeiro' => 'numMonth',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testNumMonthInvalid(): void
    {
        $array = [
            'texto' => 'jan',
            'tresDigitos' => '123',
            'treze' => '13',
            'zero' => '0',
        ];
        $rules = [
            'texto' => 'numMonth',
            'tresDigitos' => 'numMonth',
            'treze' => 'numMonth',
            'zero' => 'numMonth',
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
