<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    private function validate(array $data, array $rules): Validator
    {
        $validator = new Validator();
        $validator->set($data, $rules);
        return $validator;
    }

    private function assertValidatorErrorCount(int $expected, array $data, array $rules): void
    {
        self::assertCount($expected, $this->validate($data, $rules)->getErros());
    }

    public function testAlpha(): void
    {
        $array = ['testError' => 'a@', 'testValid' => 'aeiouAÉIÓÚ',];
        $rules = ['testError' => 'alpha', 'testValid' => 'alpha',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNoSpecial(): void
    {
        $array = ['testError' => 'aéiou', 'testValid' => 'aEiOU',];
        $rules = ['testError' => 'alphaNoSpecial', 'testValid' => 'alphaNoSpecial',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNum(): void
    {
        $array = ['testError' => 'a1B2Éí3@', 'testValid' => 'a1B2Éí3',];
        $rules = ['testError' => 'alphaNum', 'testValid' => 'alphaNum',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNumNoSpecial(): void
    {
        $array = ['testError' => 'AeioÚ123', 'testValid' => 'AeioU123',];
        $rules = ['testError' => 'alphaNumNoSpecial', 'testValid' => 'alphaNumNoSpecial',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testRgbColor(): void
    {
        $array = ['testError' => '300, 50, 255', 'testValid' => '0, 43, 233',];
        $rules = ['testError' => 'rgbColor', 'testValid' => 'rgbColor',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testValidateDdd(): void
    {
        $array = [
            'testErrorDddTwoDigits' => '60',
            'testValidTwoDigits' => '61',
            'testErrorDddTwoDigitsState' => '11',
            'testValidTwoDigitsState' => '44',
        ];
        $rules = [
            'testErrorDddTwoDigits' => 'ddd',
            'testValidTwoDigits' => 'ddd',
            'testErrorDddTwoDigitsState' => 'ddd:pr',
            'testValidTwoDigitsState' => 'ddd:pr',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testEmail(): void
    {
        $array = [
            'testError' => 'email-invalido',
            'testValid' => 'teste@example.com',
        ];
        $rules = [
            'testError' => 'email',
            'testValid' => 'email',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testEmailWithSubdomain(): void
    {
        $array = [
            'testValid' => 'user@mail.example.com',
            'testValidPlus' => 'user+tag@example.com',
        ];
        $rules = [
            'testValid' => 'email',
            'testValidPlus' => 'email',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testIdentifier(): void
    {
        $array = [
            'testError' => '123.456.789-00',
            'testValid' => '529.982.247-25',
        ];
        $rules = [
            'testError' => 'identifier',
            'testValid' => 'identifier',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testIdentifierWithoutMask(): void
    {
        $array = [
            'testError' => '12345678900',
            'testValid' => '52998224725',
        ];
        $rules = [
            'testError' => 'identifier',
            'testValid' => 'identifier',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testCompanyIdentification(): void
    {
        $array = [
            'testError' => '11.111.111/1111-11',
            'testValid' => '32.063.364/0001-07',
        ];
        $rules = [
            'testError' => 'companyIdentification',
            'testValid' => 'companyIdentification',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testIdentifierOrCompany(): void
    {
        $array = [
            'testValidCpf' => '52998224725',
            'testValidCnpj' => '32063364000107',
            'testErrorCpf' => '12345678900',
            'testErrorCnpj' => '11111111111111',
        ];
        $rules = [
            'testValidCpf' => 'identifierOrCompany',
            'testValidCnpj' => 'identifierOrCompany',
            'testErrorCpf' => 'identifierOrCompany',
            'testErrorCnpj' => 'identifierOrCompany',
        ];
        self::assertValidatorErrorCount(2, $array, $rules);
    }

    public function testIp(): void
    {
        $array = [
            'testError' => '999.999.999.999',
            'testValid' => '192.168.1.1', // NOSONAR - IP usado apenas para teste de validação
        ];
        $rules = [
            'testError' => 'ip',
            'testValid' => 'ip',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testIpv6(): void
    {
        $array = [
            'testValid' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'testValidShort' => '::1',
        ];
        $rules = [
            'testValid' => 'ip',
            'testValidShort' => 'ip',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testLower(): void
    {
        $array = [
            'testError' => 'UPPERCASE',
            'testValid' => 'lowercase',
        ];
        $rules = [
            'testError' => 'lower',
            'testValid' => 'lower',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testUpper(): void
    {
        $array = [
            'testError' => 'lowercase',
            'testValid' => 'UPPERCASE',
        ];
        $rules = [
            'testError' => 'upper',
            'testValid' => 'upper',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMac(): void
    {
        $array = [
            'testError' => 'GG:GG:GG:GG:GG:GG',
            'testValid' => '00:1A:2B:3C:4D:5E',
        ];
        $rules = [
            'testError' => 'mac',
            'testValid' => 'mac',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMin(): void
    {
        $array = [
            'testError' => 'ab',
            'testValid' => 'abcde',
        ];
        $rules = [
            'testError' => 'min:5',
            'testValid' => 'min:5',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMax(): void
    {
        $array = [
            'testError' => 'abcdefghij',
            'testValid' => 'abcde',
        ];
        $rules = [
            'testError' => 'max:5',
            'testValid' => 'max:5',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMinWords(): void
    {
        $array = [
            'testError' => 'Uma palavra',
            'testValid' => 'Três palavras aqui',
        ];
        $rules = [
            'testError' => 'minWords:3',
            'testValid' => 'minWords:3',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMaxWords(): void
    {
        $array = [
            'testError' => 'Muitas palavras aqui nesta frase',
            'testValid' => 'Duas palavras',
        ];
        $rules = [
            'testError' => 'maxWords:3',
            'testValid' => 'maxWords:3',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testPlate(): void
    {
        $array = [
            'testError' => 'ABC1234',
            'testValid' => 'ABC-1234',
        ];
        $rules = [
            'testError' => 'plate',
            'testValid' => 'plate',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testPhone(): void
    {
        $array = [
            'testError' => '1234567',
            'testValid' => '(11)98765-4321',
        ];
        $rules = [
            'testError' => 'phone',
            'testValid' => 'phone',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testPhoneWithoutMask(): void
    {
        $array = [
            'testValid10' => '1198765432',
            'testValid11' => '11987654321',
        ];
        $rules = [
            'testValid10' => 'phone',
            'testValid11' => 'phone',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testRegex(): void
    {
        $array = [
            'testError' => 'abc123',
            'testValid' => '12345',
        ];
        $rules = [
            'testError' => 'regex:/^[0-9]+$/',
            'testValid' => 'regex:/^[0-9]+$/',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testNotSpace(): void
    {
        $array = [
            'testError' => 'com espaco',
            'testValid' => 'semespaco',
        ];
        $rules = [
            'testError' => 'notSpace',
            'testValid' => 'notSpace',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testUrl(): void
    {
        $array = [
            'testError' => 'not-a-url',
            'testValid' => 'https://www.example.com',
        ];
        $rules = [
            'testError' => 'url',
            'testValid' => 'url',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testUrlVariations(): void
    {
        $array = [
            'testValidHttp' => 'http://example.com',
            'testValidWithPath' => 'https://example.com/path/to/page',
            'testValidWithQuery' => 'https://example.com?param=value',
        ];
        $rules = [
            'testValidHttp' => 'url',
            'testValidWithPath' => 'url',
            'testValidWithQuery' => 'url',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testZipCode(): void
    {
        $array = [
            'testError' => '123456',
            'testValid' => '01310-100',
        ];
        $rules = [
            'testError' => 'zipcode',
            'testValid' => 'zipcode',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testZipCodeWithoutMask(): void
    {
        $array = [
            'testValid' => '01310100',
        ];
        $rules = [
            'testValid' => 'zipcode',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testEquals(): void
    {
        $array = [
            'password' => 'secret123',
            'confirmPassword' => 'secret123',
            'wrongPassword' => 'different',
        ];
        $rules = [
            'confirmPassword' => 'equals:password',
            'wrongPassword' => 'equals:password',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testDddWithThreeDigits(): void
    {
        $array = [
            'testValid' => '011',
            'testError' => '060',
        ];
        $rules = [
            'testValid' => 'ddd',
            'testError' => 'ddd',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testAlphaWithSpaces(): void
    {
        $array = [
            'testValid' => 'Bruno Conte Developer',
        ];
        $rules = [
            'testValid' => 'alpha',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testRgbColorVariations(): void
    {
        $array = [
            'testValidNoSpaces' => '0,43,233',
            'testValidWithSpaces' => '0 , 43 , 233',
            'testValidMax' => '255, 255, 255',
            'testValidMin' => '0, 0, 0',
        ];
        $rules = [
            'testValidNoSpaces' => 'rgbColor',
            'testValidWithSpaces' => 'rgbColor',
            'testValidMax' => 'rgbColor',
            'testValidMin' => 'rgbColor',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }

    public function testRgbColorInvalidValues(): void
    {
        $array = [
            'testErrorNegative' => '-1, 0, 0',
            'testErrorOver255' => '256, 0, 0',
        ];
        $rules = [
            'testErrorNegative' => 'rgbColor',
            'testErrorOver255' => 'rgbColor',
        ];
        self::assertValidatorErrorCount(2, $array, $rules);
    }

    public function testLowerWithSpecialChars(): void
    {
        $array = [
            'testValid' => 'hello world',
            'testValidwithoutSpaces' => 'helloworld',
            'testeValidWithSpecial' => 'hello@world',
            'testErrorWithNumbers' => 'hello123',
        ];
        $rules = [
            'testValid' => 'lower',
            'testValidwithoutSpaces' => 'lower',
            'testeValidWithSpecial' => 'lower',
            'testErrorWithNumbers' => 'lower',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testUpperWithSpecialChars(): void
    {
        $array = [
            'testValid' => 'HELLO WORLD',
            'testValidWithCharacters' => 'HELLO@WORLD',
            'testErrorWithNumbers' => 'HELLO123',
        ];
        $rules = [
            'testValid' => 'upper',
            'testValidWithCharacters' => 'upper',
            'testErrorWithNumbers' => 'upper',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMinWithUnicodeCharacters(): void
    {
        $array = [
            'testValid' => 'açãõé',
            'testError' => 'ab',
        ];
        $rules = [
            'testValid' => 'min:5',
            'testError' => 'min:5',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMaxWithUnicodeCharacters(): void
    {
        $array = [
            'testValid' => 'açã',
            'testError' => 'açãõéíú',
        ];
        $rules = [
            'testValid' => 'max:5',
            'testError' => 'max:5',
        ];
        self::assertValidatorErrorCount(1, $array, $rules);
    }

    public function testMacAlternateFormats(): void
    {
        $array = [
            'testValidDash' => '00-1A-2B-3C-4D-5E',
            'testValidLowercase' => '00:1a:2b:3c:4d:5e',
        ];
        $rules = [
            'testValidDash' => 'mac',
            'testValidLowercase' => 'mac',
        ];
        self::assertValidatorErrorCount(0, $array, $rules);
    }
}
