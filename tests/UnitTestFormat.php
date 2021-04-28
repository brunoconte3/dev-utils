<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Format;
use PHPUnit\Framework\TestCase;

class UnitTestFormat extends TestCase
{
    public function testCompanyIdentification(): void
    {
        $this->assertEquals('76.027.484/0001-24', Format::companyIdentification('76027484000124'));
    }

    public function testConvertTypes(): void
    {
        $data = [
            'tratandoTipoInt' => '12',
            'tratandoTipoFloat' => '9.63',
            'tratandoTipoBoolean' => 'true',
            'tratandoTipoNumeric' => '11',
        ];

        $rules = [
            'tratandoTipoInt' => 'convert|int',
            'tratandoTipoFloat' => 'convert|float',
            'tratandoTipoBoolean' => 'convert|bool',
            'tratandoTipoNumeric' => 'convert|numeric',
        ];

        Format::convertTypes($data, $rules);
        $this->assertIsInt($data['tratandoTipoInt']);
        $this->assertIsFloat($data['tratandoTipoFloat']);
        $this->assertIsBool($data['tratandoTipoBoolean']);
        $this->assertIsNumeric($data['tratandoTipoNumeric']);
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('894.213.600-10', Format::identifier('89421360010'));
    }

    public function testIdentifierOrCompany(): void
    {
        $this->assertEquals('307.208.700-89', Format::identifierOrCompany('30720870089'));
        $this->assertEquals('12.456.571/0001-14', Format::identifierOrCompany('12456571000114'));
    }

    public function testTelephone(): void
    {
        $this->assertEquals('(44) 99999-8888', Format::telephone('44999998888'));
    }

    public function testZipCode(): void
    {
        $this->assertEquals('87047-590', Format::zipCode('87047590'));
    }

    public function testDateBrazil(): void
    {
        $this->assertEquals('10/10/2020', Format::dateBrazil('2020-10-10'));
    }

    public function testDateAmerican(): void
    {
        $this->assertEquals('2020-10-10', Format::dateAmerican('10/10/2020'));
    }

    public function testArrayToIntReference(): void
    {
        $arrayProcessed = [
            0 => 1,
            1 => 123,
            'a' => 222,
            'b' => 333,
            'c' => 0
        ];
        $this->assertEquals($arrayProcessed, Format::arrayToInt([
            0 => '1',
            1 => '123',
            'a' => '222',
            'b' => 333,
            'c' => ''
        ]));
    }

    public function testArrayToInt(): void
    {
        $arrayProcessed = [
            0 => 1,
            1 => 123,
            'a' => 222,
            'b' => 333,
            'c' => 0
        ];
        $this->assertEquals($arrayProcessed, Format::arrayToInt([
            0 => '1',
            1 => '123',
            'a' => '222',
            'b' => 333,
            'c' => ''
        ]));
    }

    public function testCurrency(): void
    {
        $this->assertEquals('1.123,45', Format::currency('1123.45'));
        $this->assertEquals('R$ 1.123,45', Format::currency('1123.45', 'R$ '));
    }

    public function testCurrencyUsd(): void
    {
        $this->assertEquals('1,123.45', Format::currencyUsd('1123.45'));
        $this->assertEquals('Usd 1,123.45', Format::currencyUsd('1123.45', 'Usd '));
    }

    public function testReturnPhoneOrAreaCode(): void
    {
        $this->assertEquals('44', Format::returnPhoneOrAreaCode('44999998888', true));
        $this->assertEquals('999998888', Format::returnPhoneOrAreaCode('44999998888'));
    }

    public function testUcwordsCharset(): void
    {
        $this->assertEquals('Açafrão Macarrão', Format::ucwordsCharset('aÇafrÃo maCaRRão'));
    }

    public function testPointOnlyValue(): void
    {
        $this->assertEquals('1350.45', Format::pointOnlyValue('1.350,45'));
    }

    public function testEmptyToNull(): void
    {
        $array = [
            0 => '1',
            'a' => '222',
            'b' => 333,
            'c' => null,
            'd' => null,
            'e' => '0',
        ];

        $this->assertEquals($array, Format::emptyToNull(
            [
                0 => '1',
                'a' => '222',
                'b' => 333,
                'c' => '',
                'd' => 'null',
                'e' => '0',
            ],
            '0'
        ));
    }

    public function testMask(): void
    {
        $this->assertEquals('1234 5678 9012 3456', Format::mask('#### #### #### ####', '1234567890123456'));
    }

    public function testOnlyNumbers(): void
    {
        $this->assertEquals('54887', Format::onlyNumbers('548Abc87@'));
    }

    public function testOnlyLettersNumbers(): void
    {
        $this->assertEquals('548Abc87', Format::onlyLettersNumbers('548Abc87@'));
    }

    public function testUpper(): void
    {
        $this->assertEquals('CARRO', Format::upper('CArrO'));
    }

    public function testLower(): void
    {
        $this->assertEquals('carro', Format::lower('CArrO'));
    }

    public function testMaskStringHidden(): void
    {
        $this->assertEquals('065.***.009.96', Format::maskStringHidden('065.775.009.96', 3, 4, '*'));
        $this->assertNull(Format::maskStringHidden('', 3, 4, '*'));
    }

    public function testReverse(): void
    {
        $this->assertEquals('ixacabA', Format::reverse('Abacaxi'));
    }

    public function testFalseToNull(): void
    {
        $this->assertEquals(null, Format::falseToNull(false));
    }

    public function testRemoveAccent(): void
    {
        $this->assertEquals('Acafrao', Format::removeAccent('Açafrão'));
        $this->assertNull(Format::removeAccent(''));
    }

    public function testWriteDateExtensive(): void
    {
        $this->assertEquals('domingo, 08 de novembro de 2020', Format::writeDateExtensive('08/11/2020'));
    }

    public function testWriteCurrencyExtensive(): void
    {
        $this->assertEquals('um real e noventa e sete centavos', Format::writeCurrencyExtensive(1.97));
    }

    public function testRestructFileArray(): void
    {
        $fileUploadSingle = [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 8488,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 8488, '1' => 818465],
        ];

        $this->assertArrayHasKey('name', Format::restructFileArray($fileUploadSingle)[0]);
        $this->assertArrayHasKey('name', Format::restructFileArray($fileUploadMultiple)[0]);
        $this->assertArrayHasKey('name', Format::restructFileArray($fileUploadMultiple)[1]);
    }

    public function testConvertTimestampBrazilToAmerican(): void
    {
        $this->assertEquals('2021-04-15 19:50:25', Format::convertTimestampBrazilToAmerican('15/04/2021 19:50:25'));
    }
}
