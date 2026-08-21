<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateCnpj;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidateCnpj::class)]
final class ValidateCnpjTest extends TestCase
{
    private const CNPJ_ZEROS_RAW = '00000000000000';
    private const CNPJ_ZEROS_MASKED = '00.000.000/0000-00';
    private const CNPJ_ONES_MASKED = '11.111.111/1111-11';
    private const CNPJ_TWOS_RAW = '22222222222222';
    private const CNPJ_VALID_NUMERIC = '32063364000107';
    private const WEIGHTS_FIRST_DIGIT = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    private const WEIGHTS_SECOND_DIGIT = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    /**
     * @param array<int, int> $weights
     */
    private static function weightedCheckDigit(string $value, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $position => $weight) {
            $sum += (ord($value[$position]) - 48) * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }

    private static function buildCnpj(string $root): string
    {
        $root = strtoupper($root);
        $first = self::weightedCheckDigit($root, self::WEIGHTS_FIRST_DIGIT);
        $second = self::weightedCheckDigit($root . (string) $first, self::WEIGHTS_SECOND_DIGIT);

        return $root . (string) $first . (string) $second;
    }

    private static function mask(string $cnpj): string
    {
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2),
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validCnpjProvider(): array
    {
        return [
            'alphanumeric readme example' => ['A1.B2C.3D4/5E6F-59'],
            'alphanumeric rfb example lowercase' => ['12abc34501de35'],
            'alphanumeric rfb example masked' => ['12.ABC.345/01DE-35'],
            'alphanumeric rfb example raw' => ['12ABC34501DE35'],
            'alphanumeric rfb example with trailing line feed' => ["12ABC34501DE35\n"],
            'noise between characters' => ['32@063#364$000%107'],
            'numeric masked' => ['57.169.078/0001-51'],
            'numeric masked with repeated blocks' => ['11.222.333/0001-81'],
            'numeric raw' => [self::CNPJ_VALID_NUMERIC],
            'numeric raw with repeated blocks' => ['11222333000181'],
            'ones root with valid check digits' => ['11111111111180'],
            'other numeric masked' => ['11.444.777/0001-61'],
            'other numeric raw' => ['11444777000161'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidCnpjProvider(): array
    {
        return [
            'accented char breaks length' => ['12.ABÇ.345/01DE-35'],
            'empty' => [''],
            'letter in first check digit' => ['A1B2C3D45E6FA0'],
            'letter in second check digit' => ['A1B2C3D45E6F0B'],
            'masked wrong first check digit' => ['55.569.078/0001-51'],
            'masked wrong second check digit' => ['11.222.333/0001-82'],
            'punctuation only' => ['..../-.'],
            'single zero' => ['0'],
            'symbols only' => ['@#$%^&*()'],
            'too long' => ['123456789012345'],
            'too short' => ['1234567890123'],
            'truncated letters' => ['AB.CDE'],
            'whitespace only' => ['   '],
            'wrong first check digit' => ['32063364000117'],
            'wrong first check digit again' => ['32063364000197'],
            'wrong second check digit' => ['32063364000108'],
            'wrong second check digit again' => ['32063364000109'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidSequenceProvider(): array
    {
        return [
            'eights' => ['88.888.888/8888-88'],
            'fives' => ['55.555.555/5555-55'],
            'fours' => ['44.444.444/4444-44'],
            'nines' => ['99.999.999/9999-99'],
            'ones' => [self::CNPJ_ONES_MASKED],
            'sevens' => ['77.777.777/7777-77'],
            'sixes' => ['66.666.666/6666-66'],
            'threes' => ['33.333.333/3333-33'],
            'twos' => ['22.222.222/2222-22'],
            'zeros' => [self::CNPJ_ZEROS_MASKED],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function alphanumericRootProvider(): array
    {
        return [
            'digits first' => ['1A2B3C4D5E6F'],
            'high letters' => ['XYZ123456ABC'],
            'letters and digits' => ['A1B2C3D45E6F'],
            'lowercase letters' => ['abcdefghijkl'],
            'repeated blocks' => ['AAAABBBBCCCC'],
            'repeated letters' => ['AAAAAAAAAAAA'],
            'sequential letters' => ['ABCDEFGHIJKL'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string|array<int, mixed>|bool, 2: bool}>
     */
    public static function exceptionProvider(): array
    {
        return [
            'array with masked value' => [self::CNPJ_ZEROS_MASKED, [self::CNPJ_ZEROS_MASKED], true],
            'array with non string element' => [self::CNPJ_ZEROS_MASKED, [null, self::CNPJ_ZEROS_RAW], true],
            'array with raw value' => [self::CNPJ_ZEROS_MASKED, [self::CNPJ_ZEROS_RAW], true],
            'array without the cnpj' => [self::CNPJ_ZEROS_MASKED, ['11111111111111', self::CNPJ_TWOS_RAW], false],
            'boolean false' => [self::CNPJ_ZEROS_MASKED, false, false],
            'boolean true' => [self::CNPJ_ZEROS_MASKED, true, false],
            'empty array' => [self::CNPJ_ZEROS_MASKED, [], false],
            'empty string' => [self::CNPJ_ZEROS_MASKED, '', false],
            'irrelevant for valid cnpj' => [self::CNPJ_VALID_NUMERIC, self::CNPJ_ZEROS_RAW, true],
            'masked string' => [self::CNPJ_ZEROS_MASKED, self::CNPJ_ZEROS_MASKED, true],
            'raw string' => [self::CNPJ_ZEROS_MASKED, self::CNPJ_ZEROS_RAW, true],
            'without valid check digits' => [self::CNPJ_ONES_MASKED, ['11111111111111'], false],
        ];
    }

    #[DataProvider('validCnpjProvider')]
    public function testAcceptsValidCnpj(string $cnpj): void
    {
        self::assertTrue(ValidateCnpj::validateCnpj($cnpj));
    }

    #[DataProvider('invalidCnpjProvider')]
    public function testRejectsInvalidCnpj(string $cnpj): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj($cnpj));
    }

    #[DataProvider('invalidSequenceProvider')]
    public function testRejectsRepeatedDigitSequences(string $cnpj): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj($cnpj));
    }

    #[DataProvider('alphanumericRootProvider')]
    public function testAcceptsCnpjBuiltWithReferenceWeights(string $root): void
    {
        $cnpj = self::buildCnpj($root);

        self::assertTrue(ValidateCnpj::validateCnpj($cnpj));
        self::assertTrue(ValidateCnpj::validateCnpj(self::mask($cnpj)));
        self::assertTrue(ValidateCnpj::validateCnpj(strtolower(self::mask($cnpj))));
    }

    #[DataProvider('alphanumericRootProvider')]
    public function testRejectsTamperedCheckDigits(string $root): void
    {
        $cnpj = self::buildCnpj($root);
        $wrongFirst = substr($cnpj, 0, 12) . (string) (((int) $cnpj[12] + 1) % 10) . $cnpj[13];
        $wrongSecond = substr($cnpj, 0, 13) . (string) (((int) $cnpj[13] + 1) % 10);

        self::assertFalse(ValidateCnpj::validateCnpj($wrongFirst));
        self::assertFalse(ValidateCnpj::validateCnpj($wrongSecond));
    }

    /**
     * @param string|array<int, mixed>|bool $cnpjException
     */
    #[DataProvider('exceptionProvider')]
    public function testHandlesExceptionList(string $cnpj, string | array | bool $cnpjException, bool $expected): void
    {
        self::assertSame($expected, ValidateCnpj::validateCnpj($cnpj, $cnpjException));
    }

    public function testLettersAreNotTreatedAsRepeatedSequences(): void
    {
        self::assertFalse(ValidateCnpj::validateCnpj(self::CNPJ_ZEROS_MASKED));
        self::assertTrue(ValidateCnpj::validateCnpj(self::buildCnpj('AAAAAAAAAAAA')));
    }
}
