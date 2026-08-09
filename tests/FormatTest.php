<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\DependencyInjection\data\DataConvertTypesBool;
use DevUtils\Format;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once './src/DependencyInjection/data/DataConvertTypesBool.php';
    }

    public function testCompanyIdentification(): void
    {
        self::assertEquals('76.027.484/0001-24', Format::companyIdentification('76027484000124'));
        self::assertEquals('BR.ASI.L20/26AA-64', Format::companyIdentification('BRASIL2026AA64'));
    }

    public function testConvertTypes(): void
    {
        $data = [
            'tratandoTipoInt' => '12',
            'tratandoTipoIntZero' => '0',
            'tratandoTipoIntNegativo' => '-8',
            'tratandoTipoFloat' => '9.63',
            'tratandoTipoBoolean' => 'true',
            'tratandoTipoNumeric' => '11',
        ];
        $rules = [
            'tratandoTipoInt' => 'convert|int',
            'tratandoTipoIntZero' => 'convert|int',
            'tratandoTipoIntNegativo' => 'convert|int',
            'tratandoTipoFloat' => 'convert|float',
            'tratandoTipoBoolean' => 'convert|bool',
            'tratandoTipoNumeric' => 'convert|numeric',
            'tratandoInexistente' => 'convert|bool',
        ];
        Format::convertTypes($data, $rules);
        self::assertIsInt($data['tratandoTipoInt']);
        self::assertIsInt($data['tratandoTipoIntZero']);
        self::assertIsInt($data['tratandoTipoIntNegativo']);
        self::assertIsFloat($data['tratandoTipoFloat']);
        self::assertIsBool($data['tratandoTipoBoolean']);
        self::assertIsNumeric($data['tratandoTipoNumeric']);
        self::assertArrayNotHasKey('tratandoInexistente', $data);
    }

    public function testConvertTypesBool(): void
    {
        $convertTypesBool = new DataConvertTypesBool();
        $data = $convertTypesBool->arrayData();
        $rules = $convertTypesBool->arrayRule();

        Format::convertTypes($data, $rules);
        self::assertIsBool($data['tratandoClasse']);
        self::assertIsBool($data['tratandoArray']);
        self::assertIsBool($data['tratandoInteiroPositivo']);
        self::assertIsBool($data['tratandoInteiroNegativo']);
        self::assertIsBool($data['tratandoStringTrue']);
        self::assertIsBool($data['tratandoStringOn']);
        self::assertIsBool($data['tratandoStringOff']);
        self::assertIsBool($data['tratandoStringYes']);
        self::assertIsBool($data['tratandoStringNo']);
        self::assertIsBool($data['tratandoStringUm']);
        self::assertIsBool($data['tratandoNull']);
        self::assertIsBool($data['tratandoInteiroZero']);
        self::assertIsBool($data['tratandoStringFalse']);
        self::assertIsBool($data['tratandoQualquerString']);
        self::assertIsBool($data['tratandoStringZero']);
        self::assertIsBool($data['tratandoStringVazio']);
    }

    public function testIdentifier(): void
    {
        self::assertEquals('894.213.600-10', Format::identifier('89421360010'));
    }

    public function testIdentifierOrCompany(): void
    {
        self::assertEquals('307.208.700-89', Format::identifierOrCompany('30720870089'));
        self::assertEquals('12.456.571/0001-14', Format::identifierOrCompany('12456571000114'));
        self::assertEquals('A1.B2C.3D4/5E6F-59', Format::identifierOrCompany('A1B2C3D45E6F59'));
    }

    public function testTelephone(): void
    {
        self::assertEquals('(44) 99999-8888', Format::telephone('44999998888'));
    }

    public function testZipCode(): void
    {
        self::assertEquals('87047-590', Format::zipCode('87047590'));
    }

    public function testDateBrazil(): void
    {
        self::assertEquals('10/10/2020', Format::dateBrazil('2020-10-10'));
    }

    public function testDateAmerican(): void
    {
        self::assertEquals('2020-10-10', Format::dateAmerican('10/10/2020'));
    }

    public function testArrayToIntReference(): void
    {
        $arrayProcessed = [
            0 => 1,
            1 => 123,
            'a' => 222,
            'b' => 333,
            'c' => 0,
        ];
        $arrayReferenced = [
            0 => '1',
            1 => '123',
            'a' => '222',
            'b' => 333,
            'c' => '',
        ];
        Format::arrayToIntReference($arrayReferenced);
        self::assertEquals($arrayProcessed, $arrayReferenced);
    }

    public function testArrayToInt(): void
    {
        $arrayProcessed = [
            0 => 1,
            1 => 123,
            'a' => 222,
            'b' => 333,
            'c' => 0,
        ];
        self::assertEquals($arrayProcessed, Format::arrayToInt([
            0 => '1',
            1 => '123',
            'a' => '222',
            'b' => 333,
            'c' => '',
        ]));
    }

    public function testCurrency(): void
    {
        self::assertEquals('1.123,45', Format::currency('1123.45'));
        self::assertEquals('R$ 1.123,45', Format::currency('1123.45', 'R$ '));
        self::assertEquals('123,00', Format::currency('123'));
        self::assertEquals('123,40', Format::currency('123.4'));
        self::assertEquals('123,40', Format::currency('123,4'));
        self::assertEquals('1,00', Format::currency('1'));
        self::assertEquals('1,00', Format::currency('1.00'));
        self::assertEquals('1,00', Format::currency('1,00'));
        self::assertEquals('1,25', Format::currency('1.25'));
        self::assertEquals('1,25', Format::currency('1,25'));
        self::assertEquals('1.400,00', Format::currency('1.400'));
        self::assertEquals('1.123,45', Format::currency(1123.45));
        self::assertEquals('R$ 1.123,45', Format::currency(1123.45, 'R$ '));
        self::assertEquals('123,00', Format::currency(123));
        self::assertEquals('123,40', Format::currency(123.4));
        self::assertEquals('1.400,00', Format::currency(1400));
    }

    public function testCurrencyUsd(): void
    {
        self::assertEquals('1,123.45', Format::currencyUsd('1123.45'));
        self::assertEquals('Usd 1,123.45', Format::currencyUsd('1123.45', 'Usd '));
    }

    public function testReturnPhoneOrAreaCode(): void
    {
        self::assertEquals('44', Format::returnPhoneOrAreaCode('44999998888', true));
        self::assertEquals('999998888', Format::returnPhoneOrAreaCode('44999998888'));
    }

    public function testUcwordsCharset(): void
    {
        self::assertEquals('Açafrão Macarrão', Format::ucwordsCharset('aÇafrÃo maCaRRão'));
    }

    public function testPointOnlyValue(): void
    {
        self::assertEquals('1350.45', Format::pointOnlyValue('1.350,45'));
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
            'f' => null,
            'g' => [1, 2,],
        ];

        self::assertSame($array, Format::emptyToNull(
            [
                0 => '1',
                'a' => '222',
                'b' => 333,
                'c' => '',
                'd' => 'null',
                'e' => '0',
                'f' => [],
                'g' => [1, 2,],
            ],
            '0',
        ));
    }

    public function testMask(): void
    {
        self::assertEquals('1234 5678 9012 3456', Format::mask('#### #### #### ####', '1234567890123456'));
    }

    public function testOnlyNumbers(): void
    {
        self::assertEquals('54887', Format::onlyNumbers('548Abc87@'));
    }

    public function testOnlyLettersNumbers(): void
    {
        self::assertEquals('548Abc87', Format::onlyLettersNumbers('548Abc87@'));
    }

    public function testUpper(): void
    {
        self::assertEquals('CARRO', Format::upper('CArrO'));
    }

    public function testLower(): void
    {
        self::assertEquals('carro', Format::lower('CArrO'));
    }

    public function testMaskStringHidden(): void
    {
        self::assertEquals('065.***.009.96', Format::maskStringHidden('065.775.009.96', 3, 4, '*'));
        self::assertNull(Format::maskStringHidden('', 3, 4, '*'));
    }

    public function testReverse(): void
    {
        self::assertEquals('ixacabA', Format::reverse('Abacaxi'));
    }

    public function testFalseToNull(): void
    {
        self::assertEquals(null, Format::falseToNull(false));
    }

    public function testRemoveAccent(): void
    {
        self::assertEquals('Acafrao', Format::removeAccent('Açafrão'));
        self::assertEquals('Acafrao com Espaco', Format::removeAccent('Açafrão com Espaço'));
        self::assertNull(Format::removeAccent(''));
        self::assertNull(Format::removeAccent(null));
    }

    public function testRemoveSpecialCharacters(): void
    {
        self::assertEquals('Acafrao ', Format::removeSpecialCharacters('Açafrão !@#$%¨&*()_+-='));
        self::assertEquals('Acafrao com Espaco ', Format::removeSpecialCharacters('Açafrão com Espaço %$#@!'));
        self::assertEquals('AcafraosemEspaco', Format::removeSpecialCharacters('Açafrão sem Espaço %$#@!', false));
        self::assertNull(Format::removeSpecialCharacters(''));
    }

    public function testWriteDateExtensive(): void
    {
        if (extension_loaded('gd')) {
            self::assertEquals('domingo, 08 de novembro de 2020', Format::writeDateExtensive('08/11/2020'));
        } else {
            self::assertFalse(extension_loaded('gd'));
        }
    }

    /**
     * @return array<string, array{0: float, 1: string}>
     */
    public static function currencyExtensiveProvider(): array
    {
        return [
            'um centavo' => [0.01, 'um centavo'],
            'somente centavos' => [0.50, 'cinquenta centavos'],
            'um real' => [1.00, 'um real'],
            'real com centavos' => [1.97, 'um real e noventa e sete centavos'],
            'teens' => [17.00, 'dezessete reais'],
            'teens em centavos' => [1.17, 'um real e dezessete centavos'],
            'centena exata' => [100.00, 'cem reais'],
            'cento e um' => [101.00, 'cento e um reais'],
            'centena com teens' => [117.00, 'cento e dezessete reais'],
            'duzentos' => [200.00, 'duzentos reais'],
            'mil exato' => [1000.00, 'mil reais'],
            'mil e dezessete' => [1017.00, 'mil e dezessete reais'],
            'dezessete mil' => [17000.00, 'dezessete mil reais'],
            'milhar com centena quebrada' => [
                3456.78,
                'três mil, quatrocentos e cinquenta e seis reais e setenta e oito centavos',
            ],
            'milhao exato' => [1000000.00, 'um milhão de reais'],
            'milhoes exatos' => [2000000.00, 'dois milhões de reais'],
            'milhao com centavos' => [1000000.23, 'um milhão de reais e vinte e três centavos'],
            'milhao e milhar' => [1500000.23, 'um milhão e quinhentos mil reais e vinte e três centavos'],
            'milhao e reais' => [1000500.00, 'um milhão e quinhentos reais'],
            'milhao e um real' => [1000001.00, 'um milhão e um real'],
            'milhoes com centena quebrada' => [3000123.00, 'três milhões, cento e vinte e três reais'],
            'dezessete milhoes' => [17000000.00, 'dezessete milhões de reais'],
            'bilhao' => [1000000000.00, 'um bilhão de reais'],
            'trilhao' => [1000000000000.00, 'um trilhão de reais'],
            'quatrilhao' => [1000000000000000.00, 'um quatrilhão de reais'],
        ];
    }

    #[DataProvider('currencyExtensiveProvider')]
    public function testWriteCurrencyExtensive(float $value, string $expected): void
    {
        self::assertSame($expected, Format::writeCurrencyExtensive($value));
    }

    public function testWriteCurrencyExtensiveRoundsBelowOneCentToZero(): void
    {
        self::assertSame('zero', Format::writeCurrencyExtensive(0.001));
    }

    public function testWriteCurrencyExtensiveAboveSupportedLimitThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Valor acima do limite suportado');
        Format::writeCurrencyExtensive(1.0e18);
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
        $resultSingle = Format::restructFileArray($fileUploadSingle);
        $resultMultiple = Format::restructFileArray($fileUploadMultiple);
        self::assertIsArray($resultSingle[0]);
        self::assertIsArray($resultMultiple[0]);
        self::assertIsArray($resultMultiple[1]);
        self::assertArrayHasKey('name', $resultSingle[0]);
        self::assertArrayHasKey('name', $resultMultiple[0]);
        self::assertArrayHasKey('name', $resultMultiple[1]);
    }

    public function testConvertTimestampBrazilToAmerican(): void
    {
        self::assertEquals('2021-04-15 19:50:25', Format::convertTimestampBrazilToAmerican('15/04/2021 19:50:25'));
    }

    public function testConvertStringToBinary(): void
    {
        self::assertEquals('1100001 1101101 1101111 1110010', Format::convertStringToBinary('amor'));
        self::assertNotSame('1100001 1101101 1101111 1110010', Format::convertStringToBinary('casa'));
    }

    public static function testSlugfy(): void
    {
        self::assertEquals('polenta-frita-com-bacon-e-parmesao', Format::slugfy('Polenta frita com Bacon e Parmesão'));
    }

    public function testCompanyIdentificationInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::companyIdentification('123');
    }

    public function testIdentifierInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::identifier('123');
    }

    public function testIdentifierOrCompanyInvalidLengthThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::identifierOrCompany('12345');
    }

    public function testTelephoneWith10Digits(): void
    {
        self::assertEquals('(44) 3333-8888', Format::telephone('4433338888'));
    }

    public function testTelephoneInvalidLengthThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::telephone('123456789');
    }

    public function testTelephoneNonNumericThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::telephone('44abc998888');
    }

    public function testZipCodeInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::zipCode('123');
    }

    public function testDateBrazilInvalidLengthThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::dateBrazil('2020');
    }

    public function testDateAmericanInvalidLengthThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::dateAmerican('10/10');
    }

    public function testDateAmericanWithoutSlash(): void
    {
        self::assertEquals('2020-10-10', Format::dateAmerican('2020-10-10'));
    }

    public function testCurrencyUsdWithFloat(): void
    {
        self::assertEquals('1,123.45', Format::currencyUsd(1123.45));
        self::assertEquals('123.00', Format::currencyUsd(123));
    }

    public function testReturnPhoneOrAreaCodeInvalidPhone(): void
    {
        self::assertFalse(Format::returnPhoneOrAreaCode('123'));
    }

    public function testReturnPhoneOrAreaCodeEmptyPhone(): void
    {
        self::assertFalse(Format::returnPhoneOrAreaCode(''));
    }

    public function testPointOnlyValueWithoutDecimal(): void
    {
        self::assertEquals('1350', Format::pointOnlyValue('1.350'));
    }

    public function testPointOnlyValueSimple(): void
    {
        self::assertEquals('100.50', Format::pointOnlyValue('100,50'));
    }

    public function testMaskStringHiddenQtdGreaterThanStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::maskStringHidden('abc', 10, 0, '*');
    }

    public function testMaskStringHiddenQtdLessThanOneThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::maskStringHidden('abc', 0, 0, '*');
    }

    public function testReverseWithAccents(): void
    {
        self::assertEquals('oãrfaçA', Format::reverse('Açafrão'));
    }

    public function testFalseToNullWithTrueValue(): void
    {
        self::assertTrue(Format::falseToNull(true));
    }

    public function testFalseToNullWithStringValue(): void
    {
        self::assertEquals('teste', Format::falseToNull('teste'));
    }

    public function testFalseToNullWithZero(): void
    {
        self::assertEquals(0, Format::falseToNull(0));
    }

    public function testWriteCurrencyExtensiveZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::writeCurrencyExtensive(0);
    }

    public function testWriteCurrencyExtensiveNegativeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::writeCurrencyExtensive(-100);
    }

    public function testConvertTimestampBrazilToAmericanInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::convertTimestampBrazilToAmerican('data-invalida');
    }

    public function testSlugfyWithMultipleSpaces(): void
    {
        self::assertSame('teste-aqui', Format::slugfy('Teste  Aqui'));
        self::assertSame('teste-aqui', Format::slugfy('  Teste   Aqui  '));
        self::assertSame('teste-aqui', Format::slugfy('Teste - Aqui'));
    }

    public function testSlugfyWithDashes(): void
    {
        self::assertEquals('teste-aqui', Format::slugfy('Teste-Aqui'));
    }

    public function testMaskWithDifferentPatterns(): void
    {
        self::assertEquals('123-456', Format::mask('###-###', '123456'));
        self::assertEquals('(12) 3456-7890', Format::mask('(##) ####-####', '1234567890'));
    }

    public function testOnlyNumbersEmpty(): void
    {
        self::assertEquals('', Format::onlyNumbers('abc'));
    }

    public function testOnlyLettersNumbersWithSpaces(): void
    {
        self::assertEquals('Abc123', Format::onlyLettersNumbers('Abc 123!'));
    }

    public function testUpperWithAccents(): void
    {
        self::assertEquals('AÇAFRÃO', Format::upper('açafrão'));
    }

    public function testLowerWithAccents(): void
    {
        self::assertEquals('açafrão', Format::lower('AÇAFRÃO'));
    }

    public function testUcwordsCharsetWithNumbers(): void
    {
        self::assertEquals('Teste 123 Aqui', Format::ucwordsCharset('TESTE 123 AQUI'));
    }

    public function testEmptyToNullWithNestedArray(): void
    {
        $result = Format::emptyToNull(['nested' => [1, 2, 3]]);
        self::assertSame([1, 2, 3], $result['nested']);
    }

    public function testConvertStringToBinaryEmpty(): void
    {
        self::assertEquals('', Format::convertStringToBinary(''));
    }

    public function testArrayToIntWithNegativeNumbers(): void
    {
        $result = Format::arrayToInt(['a' => '-10', 'b' => '-5']);
        self::assertSame(['a' => -10, 'b' => -5], $result);
    }

    public function testCurrencyWithZero(): void
    {
        self::assertEquals('0,00', Format::currency(0));
        self::assertEquals('0,00', Format::currency('0'));
    }

    public function testCurrencyUsdWithZero(): void
    {
        self::assertEquals('0.00', Format::currencyUsd(0));
    }

    public function testCurrencyKeepsNegativeSignFromString(): void
    {
        self::assertSame('-1.123,45', Format::currency('-1123.45'));
        self::assertSame('-1.123,45', Format::currency(-1123.45));
        self::assertSame('-123,40', Format::currency('-123,4'));
        self::assertSame('R$ -1.123,45', Format::currency('-1123.45', 'R$ '));
        self::assertSame('-1,123.45', Format::currencyUsd('-1123.45'));
    }

    public function testCompanyIdentificationAcceptsLowercase(): void
    {
        self::assertSame('BR.ASI.L20/26AA-64', Format::companyIdentification('brasil2026aa64'));
        self::assertSame('BR.ASI.L20/26AA-64', Format::companyIdentification('bRaSiL2026aA64'));
        self::assertSame('76.027.484/0001-24', Format::companyIdentification('76.027.484/0001-24'));
    }

    public function testTelephoneRejectsSignedNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('apenas números');
        Format::telephone('+443333888');
    }

    public function testIdentifierNonNumericThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('apenas números');
        Format::identifier('abcdefghijk');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidDateProvider(): array
    {
        return [
            'texto' => ['abcdefgh'],
            'dia impossivel' => ['31/02/2020'],
            'mes impossivel' => ['2020-13-01'],
            'barra invertida' => ['2020/10/31'],
            'numeros soltos' => ['12345678'],
        ];
    }

    #[DataProvider('invalidDateProvider')]
    public function testDateBrazilRejectsInvalidDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('data inválida');
        Format::dateBrazil($invalid);
    }

    #[DataProvider('invalidDateProvider')]
    public function testDateAmericanRejectsInvalidDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Format::dateAmerican($invalid);
    }

    #[DataProvider('invalidDateProvider')]
    public function testWriteDateExtensiveRejectsInvalidDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Format::writeDateExtensive($invalid);
    }

    public function testWriteDateExtensiveInvalidLengthThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('8 à 10 dígitos');
        Format::writeDateExtensive('2020');
    }

    public function testDateBrazilAcceptsEveryDocumentedFormat(): void
    {
        self::assertSame('10/10/2020', Format::dateBrazil('2020-10-10'));
        self::assertSame('10/10/2020', Format::dateBrazil('10/10/2020'));
        self::assertSame('12/05/2020', Format::dateBrazil('12-05-2020'));
        self::assertSame('01/01/2020', Format::dateBrazil(' 2020-01-01 '));
    }

    public function testDateAmericanAcceptsEveryDocumentedFormat(): void
    {
        self::assertSame('2020-10-10', Format::dateAmerican('10/10/2020'));
        self::assertSame('2020-10-10', Format::dateAmerican('2020-10-10'));
        self::assertSame('2020-05-12', Format::dateAmerican('12-05-2020'));
    }

    public function testConvertTypesReturnsEmptyErrorsOnSuccess(): void
    {
        $data = ['idade' => '30'];
        $errors = Format::convertTypes($data, ['idade' => 'convert|int']);

        self::assertSame([], $errors);
        self::assertSame(30, $data['idade']);
    }

    public function testConvertTypesReportsFailureInsteadOfSwallowingIt(): void
    {
        $data = ['idade' => 'trinta'];
        $errors = Format::convertTypes($data, ['idade' => 'convert|int']);

        self::assertCount(1, $errors);
        self::assertStringContainsString("campo 'idade' para int", $errors[0]);
        self::assertSame('trinta', $data['idade']);
    }

    public function testConvertTypesReportsFloatFailure(): void
    {
        $data = ['preco' => 'abc'];
        $errors = Format::convertTypes($data, ['preco' => 'convert|float']);

        self::assertCount(1, $errors);
        self::assertSame('abc', $data['preco']);
    }

    public function testConvertTypesIgnoresNonStringRule(): void
    {
        $data = ['x' => '5'];
        $errors = Format::convertTypes($data, ['x' => ['convert', 'int']]);

        self::assertSame([], $errors);
        self::assertSame('5', $data['x']);
    }

    public function testConvertTypesIgnoresRuleWithoutConvertKeyword(): void
    {
        $data = ['x' => '5'];
        $errors = Format::convertTypes($data, ['x' => 'int']);

        self::assertSame([], $errors);
        self::assertSame('5', $data['x']);
    }

    public function testConvertTypesIgnoresRuleWithoutKnownType(): void
    {
        $data = ['x' => '5'];
        $errors = Format::convertTypes($data, ['x' => 'convert|required']);

        self::assertSame([], $errors);
        self::assertSame('5', $data['x']);
    }

    public function testConvertTypesIgnoresRuleForMissingField(): void
    {
        $data = ['x' => '5'];
        $errors = Format::convertTypes($data, ['inexistente' => 'convert|int']);

        self::assertSame([], $errors);
        self::assertArrayNotHasKey('inexistente', $data);
    }

    public function testMaskStringHiddenPositionOutOfRangeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('fora do intervalo');
        Format::maskStringHidden('abcdef', 3, 99, '*');
    }

    public function testMaskStringHiddenNegativePositionThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('fora do intervalo');
        Format::maskStringHidden('abcdef', 3, -2, '*');
    }

    public function testMaskStringHiddenAtTheEndOfString(): void
    {
        self::assertSame('abc***', Format::maskStringHidden('abcdef', 3, 3, '*'));
    }

    public function testZeroIsNotTreatedAsEmpty(): void
    {
        self::assertSame('0', Format::removeAccent('0'));
        self::assertSame('0', Format::removeSpecialCharacters('0'));
        self::assertSame('0', Format::slugfy('0'));
    }

    public function testRestructFileArrayWithEmptyInput(): void
    {
        self::assertSame([], Format::restructFileArray());
        self::assertSame([], Format::restructFileArray([]));
    }

    public function testRestructFileArrayReturnsPhpUploadErrors(): void
    {
        $result = Format::restructFileArray([
            'name' => ['a.jpg'],
            'type' => ['image/jpeg'],
            'tmp_name' => [''],
            'error' => [UPLOAD_ERR_INI_SIZE],
            'size' => [0],
        ]);

        self::assertCount(1, $result);
        self::assertStringContainsString('[a.jpg]', (string) $result[0]);
    }

    public function testRestructFileArrayWithoutNameKey(): void
    {
        self::assertSame([], Format::restructFileArray(['error' => [0]]));
    }

    public function testRestructFileArrayIgnoresNonStringName(): void
    {
        self::assertSame([], Format::restructFileArray([
            'name' => [123],
            'type' => ['image/jpeg'],
            'tmp_name' => ['/tmp/x'],
            'error' => [0],
            'size' => [10],
        ]));
    }

    public function testRestructFileArrayNormalizesSingleUploadIntoList(): void
    {
        $result = Format::restructFileArray([
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 8488,
        ]);

        $file = (array) $result[0];
        self::assertCount(1, $result);
        self::assertSame('jpg__validacao_upload_v1.jpg', $file['name']);
        self::assertSame('image/jpeg', $file['type']);
        self::assertSame(8488, $file['size']);
        self::assertStringEndsWith('jpg__validacao_upload_v1.jpg', (string) $file['name_upload']);
    }

    public function testRestructFileArrayFallsBackWhenMetadataIsNotArray(): void
    {
        $result = Format::restructFileArray([
            'name' => ['a.jpg'],
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/x',
            'error' => 0,
            'size' => 10,
        ]);

        $file = (array) $result[0];
        self::assertSame('', $file['type']);
        self::assertSame('', $file['tmp_name']);
        self::assertSame(0, $file['error']);
        self::assertSame(0, $file['size']);
    }
}
