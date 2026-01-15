<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidatePhone;
use PHPUnit\Framework\TestCase;

class ValidatePhoneTest extends TestCase
{
    public function testValidate(): void
    {
        self::assertEquals(true, ValidatePhone::validate('44999999999'));
        self::assertEquals(false, ValidatePhone::validate('449999999'));
    }

    public function testValidateWithMask(): void
    {
        self::assertTrue(ValidatePhone::validate('(44) 99999-9999'));
        self::assertTrue(ValidatePhone::validate('(44) 9999-9999'));
        self::assertTrue(ValidatePhone::validate('(11) 98765-4321'));
    }

    public function testValidateWithoutMask(): void
    {
        self::assertTrue(ValidatePhone::validate('44999999999'));
        self::assertTrue(ValidatePhone::validate('4499999999'));
        self::assertTrue(ValidatePhone::validate('11987654321'));
    }

    public function testValidateTenDigits(): void
    {
        self::assertTrue(ValidatePhone::validate('1199999999'));
        self::assertTrue(ValidatePhone::validate('4433334444'));
    }

    public function testValidateElevenDigits(): void
    {
        self::assertTrue(ValidatePhone::validate('11999999999'));
        self::assertTrue(ValidatePhone::validate('44988887777'));
    }

    public function testValidateInvalidTooShort(): void
    {
        self::assertFalse(ValidatePhone::validate('449999999'));
        self::assertFalse(ValidatePhone::validate('123456789'));
        self::assertFalse(ValidatePhone::validate('12345'));
    }

    public function testValidateInvalidTooLong(): void
    {
        self::assertFalse(ValidatePhone::validate('449999999999'));
        self::assertFalse(ValidatePhone::validate('1234567890123'));
    }

    public function testValidateInvalidDDD(): void
    {
        self::assertFalse(ValidatePhone::validate('00999999999'));
        self::assertFalse(ValidatePhone::validate('01999999999'));
        self::assertFalse(ValidatePhone::validate('10999999999'));
    }

    public function testValidateEmptyString(): void
    {
        self::assertFalse(ValidatePhone::validate(''));
    }

    public function testValidateWithLetters(): void
    {
        self::assertFalse(ValidatePhone::validate('44ABCDEFGHI'));
        self::assertFalse(ValidatePhone::validate('abcdefghijk'));
    }

    public function testValidateWithSpecialChars(): void
    {
        self::assertTrue(ValidatePhone::validate('(44) 99999-9999'));
        self::assertTrue(ValidatePhone::validate('44.99999.9999'));
        self::assertTrue(ValidatePhone::validate('44-99999-9999'));
    }

    public function testValidateAllValidDDDs(): void
    {
        $validDDDs = ['11', '21', '31', '41', '51', '61', '71', '81', '91', '44', '48', '67', '98'];
        foreach ($validDDDs as $ddd) {
            $phone = $ddd . '999999999';
            self::assertTrue(ValidatePhone::validate($phone), "Falhou para DDD: $ddd");
        }
    }

    public function testValidateCellphoneWithNine(): void
    {
        self::assertTrue(ValidatePhone::validate('11999999999'));
        self::assertTrue(ValidatePhone::validate('44988887777'));
        self::assertTrue(ValidatePhone::validate('21987654321'));
    }

    public function testValidateLandline(): void
    {
        self::assertTrue(ValidatePhone::validate('1133334444'));
        self::assertTrue(ValidatePhone::validate('4433221100'));
    }

    public function testValidateWithSpaces(): void
    {
        self::assertTrue(ValidatePhone::validate('44 99999 9999'));
        self::assertTrue(ValidatePhone::validate('44 9999 9999'));
    }

    public function testValidateInvalidCellphoneStartingWithZero(): void
    {
        self::assertFalse(ValidatePhone::validate('44099999999'));
    }
}
