<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateHour;
use PHPUnit\Framework\TestCase;

class ValidateHourTest extends TestCase
{
    public function testValidateHour(): void
    {
        self::assertTrue(ValidateHour::validateHour('08:50'));
        self::assertFalse(ValidateHour::validateHour('08:5'));
    }

    public function testValidateHourEdgeCases(): void
    {
        self::assertTrue(ValidateHour::validateHour('00:00'));
        self::assertTrue(ValidateHour::validateHour('23:59'));
        self::assertTrue(ValidateHour::validateHour('12:00'));
        self::assertTrue(ValidateHour::validateHour('09:30'));
    }

    public function testValidateHourInvalidHours(): void
    {
        self::assertFalse(ValidateHour::validateHour('24:00'));
        self::assertFalse(ValidateHour::validateHour('25:00'));
        self::assertFalse(ValidateHour::validateHour('99:00'));
    }

    public function testValidateHourInvalidMinutes(): void
    {
        self::assertFalse(ValidateHour::validateHour('12:60'));
        self::assertFalse(ValidateHour::validateHour('12:99'));
        self::assertFalse(ValidateHour::validateHour('12:100'));
    }

    public function testValidateHourInvalidFormats(): void
    {
        self::assertFalse(ValidateHour::validateHour(''));
        self::assertFalse(ValidateHour::validateHour('8:50'));
        self::assertFalse(ValidateHour::validateHour('08:5'));
        self::assertFalse(ValidateHour::validateHour('0850'));
        self::assertFalse(ValidateHour::validateHour('08-50'));
        self::assertFalse(ValidateHour::validateHour('08.50'));
    }

    public function testValidateHourWithSeconds(): void
    {
        self::assertFalse(ValidateHour::validateHour('08:50:30'));
    }

    public function testValidateHourWithSpaces(): void
    {
        self::assertFalse(ValidateHour::validateHour(' 08:50'));
        self::assertFalse(ValidateHour::validateHour('08:50 '));
        self::assertFalse(ValidateHour::validateHour(' 08:50 '));
        self::assertFalse(ValidateHour::validateHour('08 :50'));
        self::assertFalse(ValidateHour::validateHour('08: 50'));
    }

    public function testValidateHourWithLetters(): void
    {
        self::assertFalse(ValidateHour::validateHour('AB:CD'));
        self::assertFalse(ValidateHour::validateHour('12:AB'));
        self::assertFalse(ValidateHour::validateHour('AB:30'));
    }

    public function testValidateHourAllValidHours(): void
    {
        for ($h = 0; $h < 24; $h++) {
            $hour = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00';
            self::assertTrue(ValidateHour::validateHour($hour), "Falhou para: $hour");
        }
    }

    public function testValidateHourAllValidMinutes(): void
    {
        for ($m = 0; $m < 60; $m++) {
            $hour = '12:' . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
            self::assertTrue(ValidateHour::validateHour($hour), "Falhou para: $hour");
        }
    }

    public function testValidateHourWithAmPm(): void
    {
        self::assertFalse(ValidateHour::validateHour('08:50 AM'));
        self::assertFalse(ValidateHour::validateHour('08:50 PM'));
        self::assertFalse(ValidateHour::validateHour('08:50AM'));
        self::assertFalse(ValidateHour::validateHour('08:50PM'));
    }

    public function testValidateHourNegativeValues(): void
    {
        self::assertFalse(ValidateHour::validateHour('-1:00'));
        self::assertFalse(ValidateHour::validateHour('12:-1'));
    }
}
