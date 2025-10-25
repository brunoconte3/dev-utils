<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateCnpj;
use PHPUnit\Framework\TestCase;

final class UnitValidateCnpjTest extends TestCase
{
    private static function charValue(string $ch): int
    {
        $n = ord($ch);
        if ($n >= 48 && $n <= 57) return $n - 48;
        if ($n >= 65 && $n <= 90) return $n - 48;
        return -1;
    }

    private static function calcDv(string $root): array
    {
        $w1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) $sum += self::charValue($root[$i]) * $w1[$i];
        $r1 = $sum % 11;
        $dv1 = ($r1 < 2) ? 0 : 11 - $r1;

        $w2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $v = $i < 12 ? self::charValue($root[$i]) : $dv1;
            $sum += $v * $w2[$i];
        }
        $r2 = $sum % 11;
        $dv2 = ($r2 < 2) ? 0 : 11 - $r2;

        return [$dv1, $dv2];
    }

    private static function makeRawCnpj(string $root): string
    {
        [$dv1, $dv2] = self::calcDv($root);
        return strtoupper($root) . $dv1 . $dv2;
    }

    private static function maskCnpj(string $raw): string
    {
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($raw, 0, 2),
            substr($raw, 2, 3),
            substr($raw, 5, 3),
            substr($raw, 8, 4),
            substr($raw, 12, 2)
        );
    }

    public function testValidNumericExamples(): void
    {
        self::assertTrue(ValidateCnpj::validateCnpj('57.169.078/0001-51'));
        self::assertFalse(ValidateCnpj::validateCnpj('55.569.078/0001-51'));
        self::assertTrue(ValidateCnpj::validateCnpj('11.222.333/0001-81'));
        self::assertFalse(ValidateCnpj::validateCnpj('11.222.333/0001-82'));
        self::assertTrue(ValidateCnpj::validateCnpj('11222333000181'));
        self::assertFalse(ValidateCnpj::validateCnpj('11222333000182'));
    }

    public function testRejectEmptyAndWhitespace(): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj(''));
        self::assertFalse(ValidateCnpj::validateCnpj('   '));
    }

    public function testValidAlphanumericMaskedAndRaw(): void
    {
        $roots = ['ABCDEFGHIJKL', 'A1B2C3D45E6F', 'XYZ123456ABC', 'AAAABBBBCCCC'];

        foreach ($roots as $root) {
            $raw = self::makeRawCnpj($root);
            $masked = self::maskCnpj($raw);
            self::assertTrue(ValidateCnpj::validateCnpj($raw));
            self::assertTrue(ValidateCnpj::validateCnpj($masked));
        }
    }

    public function testAcceptLowercase(): void
    {
        $root = 'abcDefGhijKl';
        $rawUpper = self::makeRawCnpj(strtoupper($root));
        $rawLower = strtolower($rawUpper);
        $maskedLower = strtolower(self::maskCnpj($rawUpper));

        self::assertTrue(ValidateCnpj::validateCnpj($rawLower));
        self::assertTrue(ValidateCnpj::validateCnpj($maskedLower));
    }

    public function testRejectAlphanumericWithWrongDv(): void
    {
        $root = 'XYZ123456ABC';
        $raw = self::makeRawCnpj($root);
        $tampered = substr($raw, 0, 13) . ((int)$raw[13] ^ 1);
        self::assertFalse(ValidateCnpj::validateCnpj($tampered));
    }

    public function testRejectLettersInDv(): void
    {
        $root = 'A1B2C3D45E6F';
        [$dv1, $dv2] = self::calcDv($root);
        $withLetterAtDv1 = $root . 'A' . $dv2;
        $withLetterAtDv2 = $root . $dv1 . 'B';

        self::assertFalse(ValidateCnpj::validateCnpj($withLetterAtDv1));
        self::assertFalse(ValidateCnpj::validateCnpj($withLetterAtDv2));
    }

    public function testSanitizeNoiseAndKeepAlnum(): void
    {
        $root = 'ABCDEFGHIJKL';
        $raw = self::makeRawCnpj($root);
        $masked = self::maskCnpj($raw);

        $noisyRaw = preg_replace('/(.)/', '$1!@', $raw);
        $noisyMasked = preg_replace('/(.)/', '#$1 ', $masked);

        self::assertTrue(ValidateCnpj::validateCnpj($noisyRaw));
        self::assertTrue(ValidateCnpj::validateCnpj($noisyMasked));
    }

    public function testRejectWrongLengthAfterSanitize(): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj('AB.CDE'));
        self::assertFalse(ValidateCnpj::validateCnpj('123456789012345'));
    }

    public function testRejectNumericSequences(): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj('00.000.000/0000-00'));
        self::assertFalse(ValidateCnpj::validateCnpj('11.111.111/1111-11'));
        self::assertFalse(ValidateCnpj::validateCnpj('22.222.222/2222-22'));
        self::assertFalse(ValidateCnpj::validateCnpj('33.333.333/3333-33'));
        self::assertFalse(ValidateCnpj::validateCnpj('44.444.444/4444-44'));
        self::assertFalse(ValidateCnpj::validateCnpj('55.555.555/5555-55'));
        self::assertFalse(ValidateCnpj::validateCnpj('66.666.666/6666-66'));
        self::assertFalse(ValidateCnpj::validateCnpj('77.777.777/7777-77'));
        self::assertFalse(ValidateCnpj::validateCnpj('88.888.888/8888-88'));
        self::assertFalse(ValidateCnpj::validateCnpj('99.999.999/9999-99'));
    }

    public function testWhitelistStillRequiresValidDv(): void
    {
        $raw00 = self::makeRawCnpj(str_repeat('0', 12));
        self::assertTrue(ValidateCnpj::validateCnpj(self::maskCnpj($raw00), '00000000000000'));

        self::assertFalse(ValidateCnpj::validateCnpj('11.111.111/1111-11', ['11111111111111']));
        self::assertFalse(ValidateCnpj::validateCnpj('22.222.222/2222-22', ['22222222222222']));

        $raw11 = self::makeRawCnpj(str_repeat('1', 12));
        self::assertTrue(ValidateCnpj::validateCnpj(self::maskCnpj($raw11)));

        $raw22 = self::makeRawCnpj(str_repeat('2', 12));
        self::assertTrue(ValidateCnpj::validateCnpj(self::maskCnpj($raw22)));
    }

    public function testBooleanExceptionDoesNotWhitelist(): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj('00.000.000/0000-00', true));
        self::assertFalse(ValidateCnpj::validateCnpj('11.111.111/1111-11', false));
    }

    public function testAlphanumericRootsIgnoreNumericBlacklist(): void
    {
        $root = 'AAAAAAAAAAAA';
        $raw = self::makeRawCnpj($root);
        self::assertTrue(ValidateCnpj::validateCnpj($raw));
    }
}
