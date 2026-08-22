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
    private const DATE_30_11_2020 = '30/11/2020';
    private const DATE_15_10_2020 = '15/10/2020';
    private const DATE_27_11_2020 = '27/11/2020';
    private const DATE_24_10_2020 = '24/10/2020';
    private const DATE_BIRTH = '17/04/1989';
    private const DATE_TIME_AMERICAN = '2026-07-26 12:00:00';
    private const HOUR_START_OF_DAY = '00:00:00';
    private const HOUR_END_OF_DAY = '23:59:59';
    private const HOUR_TEN = '10:00:00';
    private const HOUR_TEN_TWENTY = '10:20:01';
    private const WORD_ACCENTED = 'Açafrão';
    private const WORD_ACCENTED_MIXED_CASE = 'AçaFrão';
    private const PATH_SYSTEM_TEST = 'sistema/teste';
    private const PATH_SPLIT = '/te/ste';
    private const PATH_TEST = '/teste';

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
            'across years' => ['31/12/2019', '01/01/2020', '+1'],
            'american negative' => ['2020-10-10', '2020-10-05', '-5'],
            'american positive' => ['2020-10-01', '2020-10-11', '+10'],
            'brazilian negative' => ['14/10/2020', '25/09/2020', '-19'],
            'brazilian positive' => ['31/10/2020', self::DATE_30_11_2020, '+30'],
            'leap day' => ['28/02/2020', '01/03/2020', '+2'],
            'mixed formats' => ['31/10/2020', '2020-11-30', '+30'],
            'united states positive' => ['10/15/2020', '10/31/2020', '+16'],
            'same day' => [self::DATE_15_10_2020, self::DATE_15_10_2020, '+0'],
            'single digit parts' => ['1/1/2020', '2/1/2020', '+1'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidDateProvider(): array
    {
        return [
            'ambiguous slash iso' => ['2020/10/31'],
            'date with time' => ['31/10/2020 10:00'],
            'empty' => [''],
            'impossible day' => ['31/02/2020'],
            'impossible month' => ['2020-13-01'],
            'incomplete' => ['10/2020'],
            'not a date' => ['xx'],
            'two digit year' => ['31/10/20'],
            'whitespace only' => ['   '],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function differenceBetweenHoursProvider(): array
    {
        return [
            'full day negative' => [self::HOUR_END_OF_DAY, self::HOUR_START_OF_DAY, '-23:59:59'],
            'full day positive' => [self::HOUR_START_OF_DAY, self::HOUR_END_OF_DAY, self::HOUR_END_OF_DAY],
            'negative exact hour' => ['12:00:00', '11:00:00', '-01:00:00'],
            'negative hours and minutes' => ['23:00:05', '12:00:00', '-11:00:05'],
            'negative minutes only' => ['10:01:00', self::HOUR_TEN, '-00:01:00'],
            'negative seconds only' => ['10:00:30', self::HOUR_TEN, '-00:00:30'],
            'positive' => ['10:41:55', '12:18:23', '01:36:28'],
            'positive round' => ['08:00:00', '10:30:00', '02:30:00'],
            'single digit hour' => ['9:30:00', '10:30:00', '01:00:00'],
            'zero' => [self::HOUR_TEN, self::HOUR_TEN, self::HOUR_START_OF_DAY],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidHourProvider(): array
    {
        return [
            'empty' => [''],
            'minutes out of range' => ['10:60:00'],
            'missing seconds' => ['10:30'],
            'negative input' => ['-10:00:00'],
            'not a time' => ['abc'],
            'seconds out of range' => ['10:00:60'],
            'too many parts' => ['10:00:00:00'],
            'unpadded parts' => ['1:2:3'],
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
        Compare::daysDifferenceBetweenData($invalid, self::DATE_30_11_2020);
    }

    #[DataProvider('invalidDateProvider')]
    public function testDaysDifferenceBetweenDataRejectsInvalidEndDate(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Compare::daysDifferenceBetweenData(self::DATE_30_11_2020, $invalid);
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
        self::assertTrue(Compare::startDateLessThanEnd(self::DATE_15_10_2020, self::DATE_15_10_2020));
        self::assertTrue(Compare::startDateLessThanEnd('2020-10-01', '2020-10-11'));
        self::assertFalse(Compare::startDateLessThanEnd(self::DATE_27_11_2020, self::DATE_24_10_2020));
        self::assertFalse(Compare::startDateLessThanEnd('2020-10-10', '2020-10-05'));
    }

    public function testStartDateLessThanEndReturnsFalseWhenNotFilled(): void
    {
        self::assertFalse(Compare::startDateLessThanEnd(null, self::DATE_24_10_2020));
        self::assertFalse(Compare::startDateLessThanEnd(self::DATE_27_11_2020, null));
        self::assertFalse(Compare::startDateLessThanEnd(null, null));
        self::assertFalse(Compare::startDateLessThanEnd('', self::DATE_24_10_2020));
        self::assertFalse(Compare::startDateLessThanEnd(self::DATE_27_11_2020, ''));
        self::assertFalse(Compare::startDateLessThanEnd('', ''));
        self::assertFalse(Compare::startDateLessThanEnd('   ', self::DATE_24_10_2020));
        self::assertFalse(Compare::startDateLessThanEnd(self::DATE_27_11_2020, '   '));
    }

    #[DataProvider('invalidDateProvider')]
    public function testStartDateLessThanEndRejectsInvalidFilledDate(string $invalid): void
    {
        if (trim($invalid) === '') {
            self::assertFalse(Compare::startDateLessThanEnd($invalid, self::DATE_30_11_2020));
            return;
        }

        $this->expectException(InvalidArgumentException::class);
        Compare::startDateLessThanEnd($invalid, self::DATE_30_11_2020);
    }

    public function testStartHourLessThanEnd(): void
    {
        $msg = 'Hora Inicial não pode ser maior que a Hora Final!';

        self::assertSame($msg, Compare::startHourLessThanEnd('12:05:01', self::HOUR_TEN_TWENTY, $msg));
        self::assertSame($msg, Compare::startHourLessThanEnd('10:00:01', self::HOUR_TEN, $msg));
        self::assertNull(Compare::startHourLessThanEnd('10:05:01', '12:20:01', $msg));
        self::assertNull(Compare::startHourLessThanEnd(self::HOUR_TEN, self::HOUR_TEN, $msg));
    }

    public function testStartHourLessThanEndUsesDefaultMessage(): void
    {
        self::assertSame(
            'Hora Inicial não pode ser maior que a Hora Final!',
            Compare::startHourLessThanEnd('12:05:01', self::HOUR_TEN_TWENTY),
        );
    }

    public function testStartHourLessThanEndReturnsMessageWhenNotFilled(): void
    {
        $msgEmpty = 'Um ou mais campos horas não foram preenchidos!';

        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('', self::HOUR_TEN_TWENTY));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(self::HOUR_TEN, ''));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(null, self::HOUR_TEN_TWENTY));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(self::HOUR_TEN, null));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd(null, null));
        self::assertSame($msgEmpty, Compare::startHourLessThanEnd('   ', self::HOUR_TEN_TWENTY));
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
                Compare::startHourLessThanEnd($invalid, self::HOUR_TEN),
            );
            return;
        }

        $this->expectException(InvalidArgumentException::class);
        Compare::startHourLessThanEnd($invalid, self::HOUR_TEN);
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
        Compare::differenceBetweenHours($invalid, self::HOUR_TEN);
    }

    #[DataProvider('invalidHourProvider')]
    public function testDifferenceBetweenHoursRejectsInvalidEndHour(string $invalid): void
    {
        $this->expectException(InvalidArgumentException::class);
        Compare::differenceBetweenHours(self::HOUR_TEN, $invalid);
    }

    public function testCalculateAgeInYearsWithFixedReference(): void
    {
        $reference = self::reference(self::DATE_TIME_AMERICAN);

        self::assertSame(37, Compare::calculateAgeInYears(self::DATE_BIRTH, $reference));
        self::assertSame(37, Compare::calculateAgeInYears('1989-04-17', $reference));
        self::assertSame(0, Compare::calculateAgeInYears('26/07/2026', $reference));
    }

    public function testCalculateAgeInYearsOnBirthdayBoundary(): void
    {
        self::assertSame(36, Compare::calculateAgeInYears(self::DATE_BIRTH, self::reference('2026-04-16 23:59:59')));
        self::assertSame(37, Compare::calculateAgeInYears(self::DATE_BIRTH, self::reference('2026-04-17 00:00:00')));
        self::assertSame(37, Compare::calculateAgeInYears(self::DATE_BIRTH, self::reference('2026-04-17 23:59:59')));
    }

    public function testCalculateAgeInYearsReturnsZeroForFutureDate(): void
    {
        $reference = self::reference(self::DATE_TIME_AMERICAN);

        self::assertSame(0, Compare::calculateAgeInYears('17/04/2040', $reference));
        self::assertSame(0, Compare::calculateAgeInYears('27/07/2026', $reference));
    }

    public function testCalculateAgeInYearsAcceptsCustomTimezone(): void
    {
        $reference = self::reference(self::DATE_TIME_AMERICAN);

        self::assertSame(37, Compare::calculateAgeInYears(self::DATE_BIRTH, $reference, 'UTC'));
        self::assertSame(37, Compare::calculateAgeInYears(self::DATE_BIRTH, $reference, 'Europe/Lisbon'));
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
        self::assertTrue(Compare::checkDataEquality(self::WORD_ACCENTED, self::WORD_ACCENTED));
        self::assertFalse(Compare::checkDataEquality(self::WORD_ACCENTED_MIXED_CASE, self::WORD_ACCENTED));
    }

    public function testCheckDataEqualityCaseInsensitive(): void
    {
        self::assertTrue(Compare::checkDataEquality(self::WORD_ACCENTED_MIXED_CASE, self::WORD_ACCENTED, false));
        self::assertTrue(Compare::checkDataEquality('JOSE', 'jose', false));
        self::assertFalse(Compare::checkDataEquality(self::WORD_ACCENTED, 'Cúrcuma', false));
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
        self::assertTrue(Compare::contains(self::WORD_ACCENTED_MIXED_CASE, 'çaF'));
        self::assertTrue(Compare::contains(self::WORD_ACCENTED, 'Aça'));
        self::assertTrue(Compare::contains(self::WORD_ACCENTED, 'rão'));
        self::assertTrue(Compare::contains(self::WORD_ACCENTED, ''));
        self::assertFalse(Compare::contains(self::WORD_ACCENTED_MIXED_CASE, 'Mac'));
        self::assertFalse(Compare::contains('', 'teste'));
        self::assertFalse(Compare::contains(self::WORD_ACCENTED, 'AÇA'));
    }

    public function testCompareStringFrom(): void
    {
        self::assertTrue(Compare::compareStringFrom('sistema', self::PATH_SYSTEM_TEST, 0, 7));
        self::assertTrue(Compare::compareStringFrom('sistema', 'sistema', 0, 7));
        self::assertTrue(Compare::compareStringFrom('teste', 'um_teste_aqui', 3, 5));
        self::assertTrue(Compare::compareStringFrom('aqui', 'um_teste_aqui', -4, 4));
        self::assertFalse(Compare::compareStringFrom('xyz', 'abcdef', 0, 3));
        self::assertFalse(Compare::compareStringFrom('sistema', self::PATH_SYSTEM_TEST, 0, 4));
    }

    public function testCompareStringFromRespectsStartAndLength(): void
    {
        self::assertFalse(Compare::compareStringFrom('abc', 'abc', 99, 1));
        self::assertFalse(Compare::compareStringFrom('abc', 'abc', 1, 3));
    }

    public function testBeginUrlWith(): void
    {
        self::assertTrue(Compare::beginUrlWith(self::PATH_TEST, '/teste/variavel'));
        self::assertTrue(Compare::beginUrlWith(self::PATH_TEST, '/teste1234'));
        self::assertTrue(Compare::beginUrlWith(self::PATH_TEST, self::PATH_TEST));
        self::assertTrue(Compare::beginUrlWith('/TESTE', '/teste/variavel'));
        self::assertTrue(Compare::beginUrlWith(self::PATH_TEST, '/teste/'));
        self::assertTrue(Compare::beginUrlWith('', self::PATH_TEST));
        self::assertFalse(Compare::beginUrlWith('/teste123', '/testeasc'));
        self::assertFalse(Compare::beginUrlWith(self::PATH_TEST, '/outro/teste'));
    }

    public function testFinishUrlWith(): void
    {
        self::assertTrue(Compare::finishUrlWith(self::PATH_TEST, 'asd/teste'));
        self::assertTrue(Compare::finishUrlWith(self::PATH_TEST, self::PATH_SYSTEM_TEST));
        self::assertTrue(Compare::finishUrlWith(self::PATH_TEST, self::PATH_TEST));
        self::assertTrue(Compare::finishUrlWith('/TESTE', self::PATH_SYSTEM_TEST));
        self::assertTrue(Compare::finishUrlWith(self::PATH_TEST, 'sistema/teste/'));
        self::assertTrue(Compare::finishUrlWith('', self::PATH_TEST));
        self::assertFalse(Compare::finishUrlWith('/test', 'sistema/teste1'));
        self::assertFalse(Compare::finishUrlWith('/sistema/teste', self::PATH_TEST));
    }

    public function testUrlHelpersDoNotIgnoreSlashes(): void
    {
        self::assertFalse(Compare::beginUrlWith(self::PATH_TEST, self::PATH_SPLIT));
        self::assertFalse(Compare::finishUrlWith(self::PATH_TEST, 'sistema/te/ste'));
        self::assertTrue(Compare::beginUrlWith(self::PATH_SPLIT, self::PATH_SPLIT));
    }

    public function testUrlHelpersAreAccentInsensitiveOnCase(): void
    {
        self::assertTrue(Compare::beginUrlWith('/AÇÃO', '/ação/listar'));
        self::assertTrue(Compare::finishUrlWith('/AÇÃO', '/sistema/ação'));
    }
}
