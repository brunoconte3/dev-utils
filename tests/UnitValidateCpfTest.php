<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateCpf;
use PHPUnit\Framework\TestCase;

final class UnitValidateCpfTest extends TestCase
{
    private static function calculateVerificationDigits(string $cpfRoot): array
    {
        $firstDigitSum = 0;
        for ($position = 0, $multiplier = 10; $position < 9; $position++, $multiplier--) {
            $firstDigitSum += intval($cpfRoot[$position]) * $multiplier;
        }
        $firstRemainder = $firstDigitSum % 11;
        $firstVerificationDigit = ($firstRemainder < 2) ? 0 : 11 - $firstRemainder;

        $secondDigitSum = 0;
        for ($position = 0, $multiplier = 11; $position < 9; $position++, $multiplier--) {
            $secondDigitSum += intval($cpfRoot[$position]) * $multiplier;
        }
        $secondDigitSum += $firstVerificationDigit * 2;
        $secondRemainder = $secondDigitSum % 11;
        $secondVerificationDigit = ($secondRemainder < 2) ? 0 : 11 - $secondRemainder;

        return [$firstVerificationDigit, $secondVerificationDigit];
    }

    private static function generateRawCpf(string $cpfRoot): string
    {
        [$firstDigit, $secondDigit] = self::calculateVerificationDigits($cpfRoot);
        return $cpfRoot . $firstDigit . $secondDigit;
    }

    private static function formatCpfWithMask(string $rawCpf): string
    {
        return sprintf(
            '%s.%s.%s-%s',
            substr($rawCpf, 0, 3),
            substr($rawCpf, 3, 3),
            substr($rawCpf, 6, 3),
            substr($rawCpf, 9, 2)
        );
    }

    public function testValidNumericExamples(): void
    {
        self::assertTrue(ValidateCpf::validateCpf('257.877.760-89'));
        self::assertFalse(ValidateCpf::validateCpf('257.877.700-88'));
        self::assertTrue(ValidateCpf::validateCpf('111.444.777-35'));
        self::assertFalse(ValidateCpf::validateCpf('111.444.777-36'));
        self::assertTrue(ValidateCpf::validateCpf('11144477735'));
        self::assertFalse(ValidateCpf::validateCpf('11144477736'));
    }

    public function testRejectEmptyAndWhitespace(): void
    {
        self::assertFalse(ValidateCpf::validateCpf(''));
        self::assertFalse(ValidateCpf::validateCpf('   '));
    }

    public function testValidMaskedAndRaw(): void
    {
        $cpfRoots = ['123456789', '987654321', '111222333', '456789012'];

        foreach ($cpfRoots as $cpfRoot) {
            $rawCpf = self::generateRawCpf($cpfRoot);
            $maskedCpf = self::formatCpfWithMask($rawCpf);
            self::assertTrue(ValidateCpf::validateCpf($rawCpf));
            self::assertTrue(ValidateCpf::validateCpf($maskedCpf));
        }
    }

    public function testRejectWithWrongDv(): void
    {
        $cpfRoot = '123456789';
        $rawCpf = self::generateRawCpf($cpfRoot);
        $tamperedFirstDigit = substr($rawCpf, 0, 10) . ((int)$rawCpf[10] ^ 1);
        self::assertFalse(ValidateCpf::validateCpf($tamperedFirstDigit));

        $tamperedSecondDigit = substr($rawCpf, 0, 9) . ((int)$rawCpf[9] ^ 1) . $rawCpf[10];
        self::assertFalse(ValidateCpf::validateCpf($tamperedSecondDigit));
    }

    public function testAcceptMaskedFormat(): void
    {
        $cpfRoot = '123456789';
        $rawCpf = self::generateRawCpf($cpfRoot);
        $maskedCpf = self::formatCpfWithMask($rawCpf);

        self::assertTrue(ValidateCpf::validateCpf($rawCpf));
        self::assertTrue(ValidateCpf::validateCpf($maskedCpf));

        $cpfWithSpaces = '  ' . $maskedCpf . '  ';
        self::assertTrue(ValidateCpf::validateCpf(trim($cpfWithSpaces)));
    }

    public function testRejectWrongLengthAfterSanitize(): void
    {
        self::assertFalse(ValidateCpf::validateCpf('123.456'));
        self::assertFalse(ValidateCpf::validateCpf('123456789012'));
        self::assertFalse(ValidateCpf::validateCpf('12345678'));
    }

    public function testRejectNumericSequences(): void
    {
        self::assertFalse(ValidateCpf::validateCpf('000.000.000-00'));
        self::assertFalse(ValidateCpf::validateCpf('111.111.111-11'));
        self::assertFalse(ValidateCpf::validateCpf('222.222.222-22'));
        self::assertFalse(ValidateCpf::validateCpf('333.333.333-33'));
        self::assertFalse(ValidateCpf::validateCpf('444.444.444-44'));
        self::assertFalse(ValidateCpf::validateCpf('555.555.555-55'));
        self::assertFalse(ValidateCpf::validateCpf('666.666.666-66'));
        self::assertFalse(ValidateCpf::validateCpf('777.777.777-77'));
        self::assertFalse(ValidateCpf::validateCpf('888.888.888-88'));
        self::assertFalse(ValidateCpf::validateCpf('999.999.999-99'));
    }

    public function testRejectSequencesWithoutMask(): void
    {
        self::assertFalse(ValidateCpf::validateCpf('00000000000'));
        self::assertFalse(ValidateCpf::validateCpf('11111111111'));
        self::assertFalse(ValidateCpf::validateCpf('22222222222'));
        self::assertFalse(ValidateCpf::validateCpf('33333333333'));
        self::assertFalse(ValidateCpf::validateCpf('44444444444'));
        self::assertFalse(ValidateCpf::validateCpf('55555555555'));
        self::assertFalse(ValidateCpf::validateCpf('66666666666'));
        self::assertFalse(ValidateCpf::validateCpf('77777777777'));
        self::assertFalse(ValidateCpf::validateCpf('88888888888'));
        self::assertFalse(ValidateCpf::validateCpf('99999999999'));
    }

    public function testValidCpfsWithSpecialCases(): void
    {
        $validCpfList = [
            '12345678909',
            '11144477735',
            '25787776089',
        ];

        foreach ($validCpfList as $cpf) {
            self::assertTrue(ValidateCpf::validateCpf($cpf), "CPF $cpf deveria ser válido");
        }
    }

    public function testHandleLeadingZeros(): void
    {
        $cpfRoot = '012345678';
        $rawCpf = self::generateRawCpf($cpfRoot);
        $maskedCpf = self::formatCpfWithMask($rawCpf);

        self::assertTrue(ValidateCpf::validateCpf($rawCpf));
        self::assertTrue(ValidateCpf::validateCpf($maskedCpf));
    }

    public function testRejectInvalidFormats(): void
    {
        self::assertFalse(ValidateCpf::validateCpf('abc.def.ghi-jk'));
        self::assertFalse(ValidateCpf::validateCpf('###.###.###-##'));
        self::assertFalse(ValidateCpf::validateCpf('...---'));
    }

    public function testAcceptStandardFormats(): void
    {
        $cpfRoot = '123456789';
        $rawCpf = self::generateRawCpf($cpfRoot);

        self::assertTrue(ValidateCpf::validateCpf($rawCpf));

        $maskedCpf = self::formatCpfWithMask($rawCpf);
        self::assertTrue(ValidateCpf::validateCpf($maskedCpf));

        self::assertFalse(ValidateCpf::validateCpf(''));
    }

    public function testEdgeCases(): void
    {
        self::assertFalse(ValidateCpf::validateCpf('0'));
        self::assertFalse(ValidateCpf::validateCpf('123'));
        self::assertFalse(ValidateCpf::validateCpf('12345678901234567890'));
    }

    public function testValidCpfGeneratedByAlgorithm(): void
    {
        $testCpfRoots = [
            '100200300',
            '200300400',
            '300400500',
            '400500600',
            '500600700',
        ];

        foreach ($testCpfRoots as $cpfRoot) {
            $generatedCpf = self::generateRawCpf($cpfRoot);
            self::assertTrue(ValidateCpf::validateCpf($generatedCpf), "CPF gerado $generatedCpf deveria ser válido");

            $formattedCpf = self::formatCpfWithMask($generatedCpf);
            self::assertTrue(ValidateCpf::validateCpf($formattedCpf), "CPF mascarado $formattedCpf deveria ser válido");
        }
    }
}
