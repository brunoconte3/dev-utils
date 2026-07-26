<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DevUtils\Compare;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Compare::class)]
class CompareTest extends TestCase
{
    private const TIMEZONE_BRAZIL = 'America/Sao_Paulo';

    private static function reference(string $dateTime): DateTimeInterface
    {
        return new DateTimeImmutable($dateTime, new DateTimeZone(self::TIMEZONE_BRAZIL));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function daysDifferenceProvider(): array
    {
        return [
            'brazilian positive' => ['31/10/2020', '30/11/2020', '+30'],
            'brazilian negative' => ['14/10/2020', '25/09/2020', '-19'],
            'american positive' => ['2020-10-01', '2020-10-11', '+10'],
            'american negative' => ['2020-10-10', '2020-10-05', '-5'],
            'same day' => ['15/10/2020', '15/10/2020', '+0'],
            'mixed formats' => ['31/10/2020', '2020-11-30', '+30'],
            'leap day' => ['28/02/2020', '01/03/2020', '+2'],
            'across years' => ['31/12/2019', '01/01/2020', '+1'],
            'single digit parts' => ['1/1/2020', '2/1/2020', '+1'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidDateProvider(): array
    {
        return [
            'not a date' => ['xx'],
            'empty' => [''],
            'whitespace only' => ['   '],
            'impossible day' => ['31/02/2020'],
            'impossible month' => ['2020-13-01'],
            'date with time' => ['31/10/2020 10:00'],
            'incomplete' => ['10/2020'],
            'ambiguous slash iso' => ['2020/10/31'],
            'two digit year' => ['31/10/20'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function differenceBetweenHoursProvider(): array
    {
        return [
            'positive' => ['10:41:55', '12:18:23', '01:36:28'],
            'positive round' => ['08:00:00', '10:30:00', '02:30:00'],
            'zero' => ['10:00:00', '10:00:00', '00:00:00'],
            'negative hours and minutes' => ['23:00:05', '12:00:00', '-11:00:05'],
            'negative exact hour' => ['12:00:00', '11:00:00', '-01:00:00'],
            'negative seconds only' => ['10:00:30', '10:00:00', '-00:00:30'],
            'negative minutes only' => ['10:01:00', '10:00:00', '-00:01:00'],
            'full day positive' => ['00:00:00', '23:59:59', '23:59:59'],
            'full day negative' => ['23:59:59', '00:00:00', '-23:59:59'],
            'single digit hour' => ['9:30:00', '10:30:00', '01:00:00'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidHourProvider(): array
    {
        return [
            'missing seconds' => ['10:30'],
            'not a time' => ['abc'],
            'empty' => [''],
            'minutes out of range' => ['10:60:00'],
            'seconds out of range' => ['10:00:60'],
            'too many parts' => ['10:00:00:00'],
            'unpadded parts' => ['1:2:3'],
            'negative input' => ['-10:00:00'],
        ];
    }

    #[DataProvider('daysDifferenceProvider')]
    public function testDaysDifferenceBetweenData(string $dtIni, string $dtFin, string $expected): void
    {
        self::assertSame($expected, Compare::daysDifferenceBetweenData($dtIni, $dtFin));
    }

    #[DataProvider('invalidDateProvider')]
    public function testDaysDifferenceBetweenDataRejectsInvalidStartDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data inválida');
        Compare::daysDifferenceBetweenData($invalid, '30/11/2020');
    }

    #[DataProvider('invalidDateProvider')]
    public function testDaysDifferenceBetweenDataRejectsInvalidEndDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Compare::daysDifferenceBetweenData('30/11/2020', $invalid);
    }

    public function testDaysDifferenceIsImmuneToDaylightSaving(): void
    {
        $original = date_default_timezone_get();
        date_default_timezone_set(self::TIMEZONE_BRAZIL);

        try {
            self::assertSame('+1', Compare::daysDifferenceBetweenData('03/11/2018', '04/11/2018'));
            self::assertSame('+30', Compare::daysDifferenceBetweenData('20/10/2018', '19/11/2018'));
            self::assertSame('+1', Compare::daysDifferenceBetweenData('16/02/2019', '17/02/2019'));
        } finally {
            date_default_timezone_set($original);
        }
    }

    public function testStartDateLessThanEnd(): void
    {
        self::assertTrue(Compare::startDateLessThanEnd('01/10/2020', '04/10/2020'));
        self::assertTrue(Compare::startDateLessThanEnd('15/10/2020', '15/10/2020'));
        self::assertTrue(Compare::startDateLessThanEnd('2020-10-01', '2020-10-11'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('2020-10-10', '2020-10-05'));
    }

    public function testStartDateLessThanEndReturnsFalseWhenNotFilled(): void
    {
        self::assertFalse(Compare::startDateLessThanEnd(null, '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', null));
        self::assertFalse(Compare::startDateLessThanEnd(null, null));
        self::assertFalse(Compare::startDateLessThanEnd('', '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', ''));
        self::assertFalse(Compare::startDateLessThanEnd('', ''));
        self::assertFalse(Compare::startDateLessThanEnd('   ', '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', '   '));
    }

    #[DataProvider('invalidDateProvider')]
    public function testStartDateLessThanEndRejectsInvalidFilledDate(string $invalid): void
    {
        if (trim($invalid) === '') {
            self::assertFalse(Compare::startDateLessThanEnd($invalid, '30/11/2020'));
            return;
        }

        $this->expectException(InvalidArgumentException::class);
        Compare::startDateLessThanEnd($invalid, '30/11/2020');
    }

    public function testStartHourLessThanEnd(): void
    {
        $msg = 'Hora Inicial não pode ser maior que a Hora Final!';

        self::assertSame($msg, Compare::startHourLessThanEnd('12:05:01', '10:20:01', $msg));
        self::assertSame($msg, Compare::startHourLessThanEnd('10:00:01', '10:00:00', $msg));
        self::assertNull(Compare::startHourLessThanEnd('10:05:01', '12:20:01', $msg));
        self::assertNull(Compare::startHourLessThanEnd('10:00:00', '10:00:00', $msg));
    }

    public function testStartHourLessThanEndUsesDefaultMessage(): void
    {
        self::assertSame(
            'Hora Inicial não pode ser maior que a Hora Final!',
            Compare::startHourLessThanEnd('12:05:01', '10:20:01'),
        );
    }

    public function testStartHourLessThanEndReturnsMessageWhenNotFilled(): void
    {
        $msgEmpty = 'Um ou mais campos horas não foram preenchidos!';

        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('', '10:20:01'));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('10:00:00', ''));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(null, '10:20:01'));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('10:00:00', null));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(null, null));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('   ', '10:20:01'));
    }

    public function testStartHourLessThanEndAcceptsCustomEmptyMessage(): void
    {
        self::assertSame(
            'Informe as duas horas!',
            Compare::startHourLessThanEnd('', '', 'Ordem inválida!', 'Informe as duas horas!'),
        );
    }

    #[DataProvider('invalidHourProvider')]
    public function testStartHourLessThanEndRejectsInvalidFilledHour(string $invalid): void
    {
        if (trim($invalid) === '') {
            self::assertSame(
                'Um ou mais campos horas não foram preenchidos!',
                Compare::startHourLessThanEnd($invalid, '10:00:00'),
            );
            return;
        }

        $this->expectException(InvalidArgumentException::class);
        Compare::startHourLessThanEnd($invalid, '10:00:00');
    }

    #[DataProvider('differenceBetweenHoursProvider')]
    public function testDifferenceBetweenHours(string $hourIni, string $hourFin, string $expected): void
    {
        self::assertSame($expected, Compare::differenceBetweenHours($hourIni, $hourFin));
    }

    #[DataProvider('invalidHourProvider')]
    public function testDifferenceBetweenHoursRejectsInvalidStartHour(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Hora inválida');
        Compare::differenceBetweenHours($invalid, '10:00:00');
    }

    #[DataProvider('invalidHourProvider')]
    public function testDifferenceBetweenHoursRejectsInvalidEndHour(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Compare::differenceBetweenHours('10:00:00', $invalid);
    }

    public function testCalculateAgeInYearsWithFixedReference(): void
    {
        $reference = self::reference('2026-07-26 12:00:00');

        self::assertSame(37, Compare::calculateAgeInYears('17/04/1989', $reference));
        self::assertSame(37, Compare::calculateAgeInYears('1989-04-17', $reference));
        self::assertSame(0, Compare::calculateAgeInYears('26/07/2026', $reference));
    }

    public function testCalculateAgeInYearsOnBirthdayBoundary(): void
    {
        self::assertSame(36, Compare::calculateAgeInYears('17/04/1989', self::reference('2026-04-16 23:59:59')));
        self::assertSame(37, Compare::calculateAgeInYears('17/04/1989', self::reference('2026-04-17 00:00:00')));
        self::assertSame(37, Compare::calculateAgeInYears('17/04/1989', self::reference('2026-04-17 23:59:59')));
    }

    public function testCalculateAgeInYearsReturnsZeroForFutureDate(): void
    {
        $reference = self::reference('2026-07-26 12:00:00');

        self::assertSame(0, Compare::calculateAgeInYears('17/04/2040', $reference));
        self::assertSame(0, Compare::calculateAgeInYears('27/07/2026', $reference));
    }

    public function testCalculateAgeInYearsAcceptsCustomTimezone(): void
    {
        $reference = self::reference('2026-07-26 12:00:00');

        self::assertSame(37, Compare::calculateAgeInYears('17/04/1989', $reference, 'UTC'));
        self::assertSame(37, Compare::calculateAgeInYears('17/04/1989', $reference, 'Europe/Lisbon'));
    }

    public function testCalculateAgeInYearsDefaultsToNow(): void
    {
        $today = new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE_BRAZIL));

        self::assertSame(0, Compare::calculateAgeInYears($today->format('d/m/Y')));
        self::assertGreaterThanOrEqual(26, Compare::calculateAgeInYears('01/01/2000'));
    }

    #[DataProvider('invalidDateProvider')]
    public function testCalculateAgeInYearsRejectsInvalidDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Compare::calculateAgeInYears($invalid);
    }

    public function testCheckDataEqualityCaseSensitive(): void
    {
        self::assertTrue(Compare::checkDataEquality('Teste', 'Teste'));
        self::assertTrue(Compare::checkDataEquality('', ''));
        self::assertTrue(Compare::checkDataEquality('Açafrão', 'Açafrão'));
        self::assertFalse(Compare::checkDataEquality('AçaFrão', 'Açafrão'));
    }

    public function testCheckDataEqualityCaseInsensitive(): void
    {
        self::assertTrue(Compare::checkDataEquality('AçaFrão', 'Açafrão', false));
        self::assertTrue(Compare::checkDataEquality('JOSE', 'jose', false));
        self::assertFalse(Compare::checkDataEquality('Açafrão', 'Cúrcuma', false));
    }

    public function testCheckDataEqualityCaseInsensitiveHandlesMultibyte(): void
    {
        self::assertTrue(Compare::checkDataEquality('AÇAFRÃO', 'açafrão', false));
        self::assertTrue(Compare::checkDataEquality('AÇÚCAR', 'açúcar', false));
        self::assertTrue(Compare::checkDataEquality('JOÃO', 'joão', false));
        self::assertTrue(Compare::checkDataEquality('ÜBER', 'über', false));
        self::assertFalse(Compare::checkDataEquality('AÇAFRÃO', 'açafrao', false));
    }

    public function testContains(): void
    {
        self::assertTrue(Compare::contains('AçaFrão', 'çaF'));
        self::assertTrue(Compare::contains('Açafrão', 'Aça'));
        self::assertTrue(Compare::contains('Açafrão', 'rão'));
        self::assertTrue(Compare::contains('Açafrão', ''));
        self::assertFalse(Compare::contains('AçaFrão', 'Mac'));
        self::assertFalse(Compare::contains('', 'teste'));
        self::assertFalse(Compare::contains('Açafrão', 'AÇA'));
    }

    public function testCompareStringFrom(): void
    {
        self::assertTrue(Compare::compareStringFrom('sistema', 'sistema/teste', 0, 7));
        self::assertTrue(Compare::compareStringFrom('sistema', 'sistema', 0, 7));
        self::assertTrue(Compare::compareStringFrom('teste', 'um_teste_aqui', 3, 5));
        self::assertTrue(Compare::compareStringFrom('aqui', 'um_teste_aqui', -4, 4));
        self::assertFalse(Compare::compareStringFrom('xyz', 'abcdef', 0, 3));
        self::assertFalse(Compare::compareStringFrom('sistema', 'sistema/teste', 0, 4));
    }

    public function testCompareStringFromRespectsStartAndLength(): void
    {
        self::assertFalse(Compare::compareStringFrom('abc', 'abc', 99, 1));
        self::assertFalse(Compare::compareStringFrom('abc', 'abc', 1, 3));
    }

    public function testBeginUrlWith(): void
    {
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste/variavel'));
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste1234'));
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste'));
        self::assertTrue(Compare::beginUrlWith('/TESTE', '/teste/variavel'));
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste/'));
        self::assertTrue(Compare::beginUrlWith('', '/teste'));
        self::assertFalse(Compare::beginUrlWith('/teste123', '/testeasc'));
        self::assertFalse(Compare::beginUrlWith('/teste', '/outro/teste'));
    }

    public function testFinishUrlWith(): void
    {
        self::assertTrue(Compare::finishUrlWith('/teste', 'asd/teste'));
        self::assertTrue(Compare::finishUrlWith('/teste', 'sistema/teste'));
        self::assertTrue(Compare::finishUrlWith('/teste', '/teste'));
        self::assertTrue(Compare::finishUrlWith('/TESTE', 'sistema/teste'));
        self::assertTrue(Compare::finishUrlWith('/teste', 'sistema/teste/'));
        self::assertTrue(Compare::finishUrlWith('', '/teste'));
        self::assertFalse(Compare::finishUrlWith('/test', 'sistema/teste1'));
        self::assertFalse(Compare::finishUrlWith('/sistema/teste', '/teste'));
    }

    public function testUrlHelpersDoNotIgnoreSlashes(): void
    {
        self::assertFalse(Compare::beginUrlWith('/teste', '/te/ste'));
        self::assertFalse(Compare::finishUrlWith('/teste', 'sistema/te/ste'));
        self::assertTrue(Compare::beginUrlWith('/te/ste', '/te/ste'));
    }

    public function testUrlHelpersAreAccentInsensitiveOnCase(): void
    {
        self::assertTrue(Compare::beginUrlWith('/AÇÃO', '/ação/listar'));
        self::assertTrue(Compare::finishUrlWith('/AÇÃO', '/sistema/ação'));
    }
}
