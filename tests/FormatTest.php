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
    private const CNPJ_NUMERIC_MASKED = '76.027.484/0001-24';
    private const CNPJ_ALPHANUMERIC_MASKED = 'BR.ASI.L20/26AA-64';
    private const CNPJ_OTHER_NUMERIC_MASKED = '12.456.571/0001-14';
    private const CNPJ_OTHER_ALPHANUMERIC_MASKED = 'K7.CM7.10C/0001-84';
    private const CPF_MASKED = '894.213.600-10';
    private const CPF_OTHER_MASKED = '307.208.700-89';
    private const RULE_CONVERT_INT = 'convert|int';
    private const PHONE_UNMASKED = '44999998888';
    private const PHONE_MASKED = '(44) 99999-8888';
    private const PHONE_LANDLINE_MASKED = '(44) 3333-8888';
    private const DATE_BRAZIL = '10/10/2020';
    private const DATE_AMERICAN = '2020-10-10';
    private const VALUE_DECIMAL = '1123.45';
    private const VALUE_DECIMAL_USD = '1,123.45';
    private const VALUE_DECIMAL_NEGATIVE = '-1123.45';
    private const VALUE_CURRENCY_BRAZIL = '123,40';
    private const VALUE_POINT_ONLY = '1350.45';
    private const ZIP_CODE_MASKED = '87020-000';
    private const MESSAGE_ONLY_NUMBERS = 'apenas números';
    private const FILE_NAME_JPG = 'JPG - Validação upload v.1.jpg';
    private const MIME_JPEG = 'image/jpeg';
    private const TMP_PATH_JPG = '/tmp/phpODnLGo';

    public function testCompanyIdentification(): void
    {
        self::assertEquals(self::CNPJ_NUMERIC_MASKED, Format::companyIdentification('76027484000124'));
        self::assertEquals(self::CNPJ_ALPHANUMERIC_MASKED, Format::companyIdentification('BRASIL2026AA64'));
    }

    public function testConvertTypes(): void
    {
        $data = [
            'treatingBooleanType' => 'true',
            'handlingFloatType' => '9.63',
            'treatingIntType' => '12',
            'treatingNegativeIntType' => '-8',
            'treatingZeroIntType' => '0',
            'handlingNumericType' => '11',
        ];
        $rules = [
            'nonExistentField' => 'convert|bool',
            'treatingBooleanType' => 'convert|bool',
            'handlingFloatType' => 'convert|float',
            'treatingIntType' => self::RULE_CONVERT_INT,
            'treatingNegativeIntType' => self::RULE_CONVERT_INT,
            'treatingZeroIntType' => self::RULE_CONVERT_INT,
            'handlingNumericType' => 'convert|numeric',
        ];
        Format::convertTypes($data, $rules);
        self::assertIsInt($data['treatingIntType']);
        self::assertIsInt($data['treatingZeroIntType']);
        self::assertIsInt($data['treatingNegativeIntType']);
        self::assertIsFloat($data['handlingFloatType']);
        self::assertIsBool($data['treatingBooleanType']);
        self::assertIsNumeric($data['handlingNumericType']);
        self::assertArrayNotHasKey('nonExistentField', $data);
    }

    public function testConvertTypesBool(): void
    {
        $convertTypesBool = new DataConvertTypesBool();
        $data = $convertTypesBool->arrayData();
        $rules = $convertTypesBool->arrayRule();

        Format::convertTypes($data, $rules);
        self::assertIsBool($data['handlingClass']);
        self::assertIsBool($data['handlingArray']);
        self::assertIsBool($data['handlingPositiveInteger']);
        self::assertIsBool($data['handlingNegativeInteger']);
        self::assertIsBool($data['handlingStringTrue']);
        self::assertIsBool($data['handlingStringOn']);
        self::assertIsBool($data['handlingStringOff']);
        self::assertIsBool($data['handlingStringYes']);
        self::assertIsBool($data['handlingStringNo']);
        self::assertIsBool($data['handlingStringOne']);
        self::assertIsBool($data['handlingNull']);
        self::assertIsBool($data['handlingZeroInteger']);
        self::assertIsBool($data['handlingStringFalse']);
        self::assertIsBool($data['handlingAnyString']);
        self::assertIsBool($data['handlingStringZero']);
        self::assertIsBool($data['handlingEmptyString']);
    }

    public function testIdentifier(): void
    {
        self::assertEquals(self::CPF_MASKED, Format::identifier('89421360010'));
    }

    public function testIdentifierAcceptsMaskedValue(): void
    {
        self::assertSame(self::CPF_MASKED, Format::identifier(self::CPF_MASKED));
        self::assertSame('067.981.009-96', Format::identifier('067.981.009-96'));
        self::assertSame(self::CPF_OTHER_MASKED, Format::identifier('307 208 700 89'));
    }

    public function testIdentifierOrCompany(): void
    {
        self::assertEquals(self::CPF_OTHER_MASKED, Format::identifierOrCompany('30720870089'));
        self::assertEquals(self::CNPJ_OTHER_NUMERIC_MASKED, Format::identifierOrCompany('12456571000114'));
        self::assertEquals('A1.B2C.3D4/5E6F-59', Format::identifierOrCompany('A1B2C3D45E6F59'));
    }

    public function testIdentifierOrCompanyAcceptsMaskedCompany(): void
    {
        self::assertSame(self::CNPJ_NUMERIC_MASKED, Format::identifierOrCompany(self::CNPJ_NUMERIC_MASKED));
        self::assertSame(self::CNPJ_ALPHANUMERIC_MASKED, Format::identifierOrCompany(self::CNPJ_ALPHANUMERIC_MASKED));
        self::assertSame(
            self::CNPJ_OTHER_ALPHANUMERIC_MASKED,
            Format::identifierOrCompany(self::CNPJ_OTHER_ALPHANUMERIC_MASKED),
        );
        self::assertSame(self::CNPJ_OTHER_NUMERIC_MASKED, Format::identifierOrCompany(self::CNPJ_OTHER_NUMERIC_MASKED));
    }

    public function testIdentifierOrCompanyAcceptsLowercaseMaskedCompany(): void
    {
        self::assertSame(self::CNPJ_ALPHANUMERIC_MASKED, Format::identifierOrCompany('br.asi.l20/26aa-64'));
        self::assertSame(self::CNPJ_OTHER_ALPHANUMERIC_MASKED, Format::identifierOrCompany('k7.cm7.10c/0001-84'));
    }

    public function testIdentifierOrCompanyAcceptsMaskedIdentifier(): void
    {
        self::assertSame(self::CPF_OTHER_MASKED, Format::identifierOrCompany(self::CPF_OTHER_MASKED));
        self::assertSame(self::CPF_MASKED, Format::identifierOrCompany(self::CPF_MASKED));
    }

    public function testIdentifierOrCompanyIgnoresSeparatorNoise(): void
    {
        self::assertSame(self::CPF_OTHER_MASKED, Format::identifierOrCompany('307 208 700 89'));
        self::assertSame(self::CNPJ_NUMERIC_MASKED, Format::identifierOrCompany('76027484/0001-24'));
    }

    public function testIdentifierOrCompanyRejectsMaskedValueWithWrongLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('identifierOrCompany => Valor precisa ser um CPF ou CNPJ!');
        Format::identifierOrCompany('12.345/6789-0');
    }

    public function testTelephone(): void
    {
        self::assertEquals(self::PHONE_MASKED, Format::telephone(self::PHONE_UNMASKED));
    }

    public function testZipCode(): void
    {
        self::assertEquals('87047-590', Format::zipCode('87047590'));
    }

    public function testDateBrazil(): void
    {
        self::assertEquals(self::DATE_BRAZIL, Format::dateBrazil(self::DATE_AMERICAN));
    }

    public function testDateAmerican(): void
    {
        self::assertEquals(self::DATE_AMERICAN, Format::dateAmerican(self::DATE_BRAZIL));
    }

    public function testArrayToIntReference(): void
    {
        $arrayProcessed = [
            'a' => 222,
            'b' => 333,
            'c' => 0,
            0 => 1,
            1 => 123,
        ];
        $arrayReferenced = [
            'a' => '222',
            'b' => 333,
            'c' => '',
            0 => '1',
            1 => '123',
        ];
        Format::arrayToIntReference($arrayReferenced);
        self::assertEquals($arrayProcessed, $arrayReferenced);
    }

    public function testArrayToInt(): void
    {
        $arrayProcessed = [
            'a' => 222,
            'b' => 333,
            'c' => 0,
            0 => 1,
            1 => 123,
        ];
        self::assertEquals($arrayProcessed, Format::arrayToInt([
            'a' => '222',
            'b' => 333,
            'c' => '',
            0 => '1',
            1 => '123',
        ]));
    }

    public function testCurrency(): void
    {
        self::assertEquals('1.123,45', Format::currency(self::VALUE_DECIMAL));
        self::assertEquals('R$ 1.123,45', Format::currency(self::VALUE_DECIMAL, 'R$ '));
        self::assertEquals('123,00', Format::currency('123'));
        self::assertEquals(self::VALUE_CURRENCY_BRAZIL, Format::currency('123.4'));
        self::assertEquals(self::VALUE_CURRENCY_BRAZIL, Format::currency('123,4'));
        self::assertEquals('1,00', Format::currency('1'));
        self::assertEquals('1,00', Format::currency('1.00'));
        self::assertEquals('1,00', Format::currency('1,00'));
        self::assertEquals('1,25', Format::currency('1.25'));
        self::assertEquals('1,25', Format::currency('1,25'));
        self::assertEquals('1.400,00', Format::currency('1.400'));
        self::assertEquals('1.123,45', Format::currency(1123.45));
        self::assertEquals('R$ 1.123,45', Format::currency(1123.45, 'R$ '));
        self::assertEquals('123,00', Format::currency(123));
        self::assertEquals(self::VALUE_CURRENCY_BRAZIL, Format::currency(123.4));
        self::assertEquals('1.400,00', Format::currency(1400));
    }

    public function testCurrencyUsd(): void
    {
        self::assertEquals(self::VALUE_DECIMAL_USD, Format::currencyUsd(self::VALUE_DECIMAL));
        self::assertEquals('Usd 1,123.45', Format::currencyUsd(self::VALUE_DECIMAL, 'Usd '));
    }

    public function testReturnPhoneOrAreaCode(): void
    {
        self::assertEquals('44', Format::returnPhoneOrAreaCode(self::PHONE_UNMASKED, true));
        self::assertEquals('999998888', Format::returnPhoneOrAreaCode(self::PHONE_UNMASKED));
    }

    public function testUcwordsCharset(): void
    {
        self::assertEquals('Açafrão Macarrão', Format::ucwordsCharset('aÇafrÃo maCaRRão'));
    }

    public function testPointOnlyValue(): void
    {
        self::assertEquals(self::VALUE_POINT_ONLY, Format::pointOnlyValue('1.350,45'));
    }

    public function testEmptyToNull(): void
    {
        $array = [
            'a' => '222',
            'b' => 333,
            'c' => null,
            'd' => null,
            'e' => '0',
            'f' => null,
            'g' => [1, 2,],
            0 => '1',
        ];

        self::assertSame($array, Format::emptyToNull(
            [
                'a' => '222',
                'b' => 333,
                'c' => '',
                'd' => 'null',
                'e' => '0',
                'f' => [],
                'g' => [1, 2,],
                0 => '1',
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
            'billion' => [1000000000.00, 'um bilhão de reais'],
            'hundred with teens' => [117.00, 'cento e dezessete reais'],
            'exact hundred' => [100.00, 'cem reais'],
            'one hundred and one' => [101.00, 'cento e um reais'],
            'seventeen thousand' => [17000.00, 'dezessete mil reais'],
            'seventeen million' => [17000000.00, 'dezessete milhões de reais'],
            'two hundred' => [200.00, 'duzentos reais'],
            'thousand and seventeen' => [1017.00, 'mil e dezessete reais'],
            'exact thousand' => [1000.00, 'mil reais'],
            'million with cents' => [1000000.23, 'um milhão de reais e vinte e três centavos'],
            'million and thousand' => [1500000.23, 'um milhão e quinhentos mil reais e vinte e três centavos'],
            'million and reais' => [1000500.00, 'um milhão e quinhentos reais'],
            'million and one real' => [1000001.00, 'um milhão e um real'],
            'exact million' => [1000000.00, 'um milhão de reais'],
            'thousand with broken hundred' => [
                3456.78,
                'três mil, quatrocentos e cinquenta e seis reais e setenta e oito centavos',
            ],
            'millions with broken hundred' => [3000123.00, 'três milhões, cento e vinte e três reais'],
            'exact millions' => [2000000.00, 'dois milhões de reais'],
            'quadrillion' => [1000000000000000.00, 'um quatrilhão de reais'],
            'real with cents' => [1.97, 'um real e noventa e sete centavos'],
            'cents only' => [0.50, 'cinquenta centavos'],
            'teens' => [17.00, 'dezessete reais'],
            'teens in cents' => [1.17, 'um real e dezessete centavos'],
            'trillion' => [1000000000000.00, 'um trilhão de reais'],
            'one cent' => [0.01, 'um centavo'],
            'one real' => [1.00, 'um real'],
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
            'error' => 0,
            'name' => self::FILE_NAME_JPG,
            'size' => 8488,
            'tmp_name' => self::TMP_PATH_JPG,
            'type' => self::MIME_JPEG,
        ];

        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => 'PDF - Validação upload v.1.pdf'],
            'size' => ['0' => 8488, '1' => 818465],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => '/tmp/phpfmb0tL'],
            'type' => ['0' => self::MIME_JPEG, '1' => 'application/pdf'],
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

    public static function testSlugify(): void
    {
        self::assertEquals('polenta-frita-com-bacon-e-parmesao', Format::slugify('Polenta frita com Bacon e Parmesão'));
    }

    public function testDeprecatedSlugfyStillDelegatesToSlugify(): void
    {
        self::assertSame(Format::slugify('Polenta frita e Parmesão'), Format::slugfy('Polenta frita e Parmesão'));
        self::assertSame('teste-aqui', Format::slugfy('Teste  Aqui'));
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
        self::assertEquals(self::PHONE_LANDLINE_MASKED, Format::telephone('4433338888'));
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

    public function testTelephoneAcceptsMaskedValue(): void
    {
        self::assertSame(self::PHONE_MASKED, Format::telephone(self::PHONE_MASKED));
        self::assertSame(self::PHONE_MASKED, Format::telephone('(44)99999-8888'));
        self::assertSame(self::PHONE_MASKED, Format::telephone('44 99999-8888'));
        self::assertSame(self::PHONE_LANDLINE_MASKED, Format::telephone(self::PHONE_LANDLINE_MASKED));
    }

    public function testTelephoneRejectsMaskedValueWithWrongLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('10 ou 11 números');
        Format::telephone('+55 44 99999-8888');
    }

    public function testTelephoneRejectsMaskedValueWithLetters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(self::MESSAGE_ONLY_NUMBERS);
        Format::telephone('(44) abcd-efgh');
    }

    public function testZipCodeAcceptsMaskedValue(): void
    {
        self::assertSame(self::ZIP_CODE_MASKED, Format::zipCode(self::ZIP_CODE_MASKED));
        self::assertSame(self::ZIP_CODE_MASKED, Format::zipCode('87.020-000'));
        self::assertSame(self::ZIP_CODE_MASKED, Format::zipCode('87020 000'));
    }

    public function testIdentifierRejectsSurroundingSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('identifier não pode conter espaços no início ou no fim!');
        Format::identifier(' ' . self::CPF_MASKED . ' ');
    }

    public function testIdentifierRejectsLeadingSpaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('identifier não pode conter espaços no início ou no fim!');
        Format::identifier(' 89421360010');
    }

    public function testIdentifierOrCompanyRejectsSurroundingSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('identifierOrCompany não pode conter espaços no início ou no fim!');
        Format::identifierOrCompany(' 76.027.484/0001-24 ');
    }

    public function testCompanyIdentificationRejectsTrailingSpace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('companyIdentification não pode conter espaços no início ou no fim!');
        Format::companyIdentification('76.027.484/0001-24 ');
    }

    public function testTelephoneRejectsSurroundingSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('telephone não pode conter espaços no início ou no fim!');
        Format::telephone(' (44) 99999-8888');
    }

    public function testZipCodeRejectsSurroundingSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('zipCode não pode conter espaços no início ou no fim!');
        Format::zipCode(' 87020-000 ');
    }

    public function testZipCodeRejectsTrailingLineBreak(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('zipCode não pode conter espaços no início ou no fim!');
        Format::zipCode("87020-000\n");
    }

    public function testZipCodeNonNumericThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(self::MESSAGE_ONLY_NUMBERS);
        Format::zipCode('abcdefgh');
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
        self::assertEquals(self::DATE_AMERICAN, Format::dateAmerican(self::DATE_AMERICAN));
    }

    public function testDateAmericanAcceptsUnitedStatesFormat(): void
    {
        self::assertSame('2024-12-31', Format::dateAmerican('12/31/2024'));
        self::assertSame('2024-01-15', Format::dateAmerican('01/15/2024'));
    }

    public function testDateAmericanKeepsBrazilianReadingWhenAmbiguous(): void
    {
        self::assertSame('2024-06-05', Format::dateAmerican('05/06/2024'));
    }

    public function testCurrencyUsdWithFloat(): void
    {
        self::assertEquals(self::VALUE_DECIMAL_USD, Format::currencyUsd(1123.45));
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

    public function testMaskStringHiddenLengthGreaterThanStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::maskStringHidden('abc', 10, 0, '*');
    }

    public function testMaskStringHiddenLengthLessThanOneThrowsException(): void
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

    public function testSlugifyWithMultipleSpaces(): void
    {
        self::assertSame('teste-aqui', Format::slugify('Teste  Aqui'));
        self::assertSame('teste-aqui', Format::slugify('  Teste   Aqui  '));
        self::assertSame('teste-aqui', Format::slugify('Teste - Aqui'));
    }

    public function testSlugifyWithDashes(): void
    {
        self::assertEquals('teste-aqui', Format::slugify('Teste-Aqui'));
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
        self::assertSame('-1.123,45', Format::currency(self::VALUE_DECIMAL_NEGATIVE));
        self::assertSame('-1.123,45', Format::currency(-1123.45));
        self::assertSame('-123,40', Format::currency('-123,4'));
        self::assertSame('R$ -1.123,45', Format::currency(self::VALUE_DECIMAL_NEGATIVE, 'R$ '));
        self::assertSame('-1,123.45', Format::currencyUsd(self::VALUE_DECIMAL_NEGATIVE));
    }

    public function testCurrencyRejectsValueWithoutDigits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('currency precisa conter ao menos um número!');
        Format::currency('abc');
    }

    public function testCurrencyUsdRejectsValueWithoutDigits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('currencyUsd precisa conter ao menos um número!');
        Format::currencyUsd('R$');
    }

    public function testCurrencyWithBlankStringReturnsZero(): void
    {
        self::assertSame('0,00', Format::currency(''));
        self::assertSame('0,00', Format::currency('   '));
        self::assertSame('0.00', Format::currencyUsd(''));
    }

    public function testPointOnlyValueWithBrazilianFormat(): void
    {
        self::assertSame(self::VALUE_POINT_ONLY, Format::pointOnlyValue('1.350,45'));
        self::assertSame('1350', Format::pointOnlyValue('1.350'));
        self::assertSame('100.50', Format::pointOnlyValue('100,50'));
        self::assertSame('1234567', Format::pointOnlyValue('1.234.567'));
        self::assertSame(self::VALUE_POINT_ONLY, Format::pointOnlyValue('R$ 1.350,45'));
        self::assertSame('', Format::pointOnlyValue('abc'));
        self::assertSame('', Format::pointOnlyValue(''));
    }

    public function testPointOnlyValueWithAmericanFormat(): void
    {
        self::assertSame(self::VALUE_DECIMAL, Format::pointOnlyValue(self::VALUE_DECIMAL));
        self::assertSame(self::VALUE_DECIMAL, Format::pointOnlyValue(self::VALUE_DECIMAL_USD));
        self::assertSame('1234567.89', Format::pointOnlyValue('1,234,567.89'));
        self::assertSame('12.5', Format::pointOnlyValue('12.5'));
        self::assertSame('0.99', Format::pointOnlyValue('0.99'));
        self::assertSame('1123', Format::pointOnlyValue('1123'));
    }

    public function testCompanyIdentificationAcceptsLowercase(): void
    {
        self::assertSame(self::CNPJ_ALPHANUMERIC_MASKED, Format::companyIdentification('brasil2026aa64'));
        self::assertSame(self::CNPJ_ALPHANUMERIC_MASKED, Format::companyIdentification('bRaSiL2026aA64'));
        self::assertSame(self::CNPJ_NUMERIC_MASKED, Format::companyIdentification(self::CNPJ_NUMERIC_MASKED));
    }

    public function testTelephoneRejectsSignedNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('10 ou 11 números');
        Format::telephone('+443333888');
    }

    public function testIdentifierNonNumericThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(self::MESSAGE_ONLY_NUMBERS);
        Format::identifier('abcdefghijk');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidDateProvider(): array
    {
        return [
            'slash instead of dash' => ['2020/10/31'],
            'impossible day' => ['31/02/2020'],
            'impossible month' => ['2020-13-01'],
            'loose numbers' => ['12345678'],
            'text' => ['abcdefgh'],
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
        self::assertSame(self::DATE_BRAZIL, Format::dateBrazil(self::DATE_AMERICAN));
        self::assertSame(self::DATE_BRAZIL, Format::dateBrazil(self::DATE_BRAZIL));
        self::assertSame('12/05/2020', Format::dateBrazil('12-05-2020'));
        self::assertSame('01/01/2020', Format::dateBrazil(' 2020-01-01 '));
    }

    public function testDateAmericanAcceptsEveryDocumentedFormat(): void
    {
        self::assertSame(self::DATE_AMERICAN, Format::dateAmerican(self::DATE_BRAZIL));
        self::assertSame(self::DATE_AMERICAN, Format::dateAmerican(self::DATE_AMERICAN));
        self::assertSame('2020-05-12', Format::dateAmerican('12-05-2020'));
    }

    public function testConvertTypesReturnsEmptyErrorsOnSuccess(): void
    {
        $data = ['age' => '30'];
        $errors = Format::convertTypes($data, ['age' => self::RULE_CONVERT_INT]);

        self::assertSame([], $errors);
        self::assertSame(30, $data['age']);
    }

    public function testConvertTypesReportsFailureInsteadOfSwallowingIt(): void
    {
        $data = ['age' => 'trinta'];
        $errors = Format::convertTypes($data, ['age' => self::RULE_CONVERT_INT]);

        self::assertCount(1, $errors);
        self::assertStringContainsString("campo 'age' para int", $errors[0]);
        self::assertSame('trinta', $data['age']);
    }

    public function testConvertTypesReportsFloatFailure(): void
    {
        $data = ['price' => 'abc'];
        $errors = Format::convertTypes($data, ['price' => 'convert|float']);

        self::assertCount(1, $errors);
        self::assertSame('abc', $data['price']);
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
        $errors = Format::convertTypes($data, ['nonExistent' => self::RULE_CONVERT_INT]);

        self::assertSame([], $errors);
        self::assertArrayNotHasKey('nonExistent', $data);
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
        self::assertSame('0', Format::slugify('0'));
    }

    public function testRestructFileArrayWithEmptyInput(): void
    {
        self::assertSame([], Format::restructFileArray());
        self::assertSame([], Format::restructFileArray([]));
    }

    public function testRestructFileArrayReturnsPhpUploadErrors(): void
    {
        $result = Format::restructFileArray([
            'error' => [UPLOAD_ERR_INI_SIZE],
            'name' => ['a.jpg'],
            'size' => [0],
            'tmp_name' => [''],
            'type' => [self::MIME_JPEG],
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
            'error' => [0],
            'name' => [123],
            'size' => [10],
            'tmp_name' => ['/tmp/x'],
            'type' => [self::MIME_JPEG],
        ]));
    }

    public function testRestructFileArrayNormalizesSingleUploadIntoList(): void
    {
        $result = Format::restructFileArray([
            'error' => 0,
            'name' => self::FILE_NAME_JPG,
            'size' => 8488,
            'tmp_name' => self::TMP_PATH_JPG,
            'type' => self::MIME_JPEG,
        ]);

        $file = (array) $result[0];
        self::assertCount(1, $result);
        self::assertSame('jpg__validacao_upload_v1.jpg', $file['name']);
        self::assertSame(self::MIME_JPEG, $file['type']);
        self::assertSame(8488, $file['size']);
        self::assertStringEndsWith('jpg__validacao_upload_v1.jpg', (string) $file['name_upload']);
    }

    public function testRestructFileArrayFallsBackWhenMetadataIsNotArray(): void
    {
        $result = Format::restructFileArray([
            'error' => 0,
            'name' => ['a.jpg'],
            'size' => 10,
            'tmp_name' => '/tmp/x',
            'type' => self::MIME_JPEG,
        ]);

        $file = (array) $result[0];
        self::assertSame('', $file['type']);
        self::assertSame('', $file['tmp_name']);
        self::assertSame(0, $file['error']);
        self::assertSame(0, $file['size']);
    }
}
