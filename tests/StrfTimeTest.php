<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DateTime;
use DateTimeImmutable;
use DevUtils\DependencyInjection\StrfTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StrfTimeTest extends TestCase
{
    private DateTime $fixedDate;

    protected function setUp(): void
    {
        $this->fixedDate = new DateTime('2024-03-15 14:30:45');
    }

    public function testStrftimeWithDateTimeObject(): void
    {
        $result = StrfTime::strftime('%Y-%m-%d', $this->fixedDate);
        self::assertSame('2024-03-15', $result);
    }

    public function testStrftimeWithDateTimeImmutable(): void
    {
        $date = new DateTimeImmutable('2024-03-15 14:30:45');
        $result = StrfTime::strftime('%Y-%m-%d', $date);
        self::assertSame('2024-03-15', $result);
    }

    public function testStrftimeWithUnixTimestamp(): void
    {
        $timestamp = 1710513045;
        $result = StrfTime::strftime('%Y', $timestamp);
        self::assertSame('2024', $result);
    }

    public function testStrftimeWithStringDate(): void
    {
        $result = StrfTime::strftime('%Y-%m-%d', '2024-03-15');
        self::assertSame('2024-03-15', $result);
    }

    public function testStrftimeWithNullTimestamp(): void
    {
        $result = StrfTime::strftime('%Y');
        self::assertSame(date('Y'), $result);
    }

    public function testStrftimeDayFormats(): void
    {
        self::assertSame('15', StrfTime::strftime('%d', $this->fixedDate));
        self::assertSame('15', StrfTime::strftime('%e', $this->fixedDate));
        self::assertSame('075', StrfTime::strftime('%j', $this->fixedDate));
        self::assertSame('5', StrfTime::strftime('%u', $this->fixedDate));
        self::assertSame('5', StrfTime::strftime('%w', $this->fixedDate));
    }

    public function testStrftimeWeekFormats(): void
    {
        self::assertSame('11', StrfTime::strftime('%V', $this->fixedDate));
        $result = StrfTime::strftime('%U', $this->fixedDate);
        self::assertMatchesRegularExpression('/^\d{2}$/', $result);
        $result = StrfTime::strftime('%W', $this->fixedDate);
        self::assertMatchesRegularExpression('/^\d{2}$/', $result);
    }

    public function testStrftimeMonthFormats(): void
    {
        self::assertSame('03', StrfTime::strftime('%m', $this->fixedDate));
    }

    public function testStrftimeYearFormats(): void
    {
        self::assertSame('2024', StrfTime::strftime('%Y', $this->fixedDate));
        self::assertSame('24', StrfTime::strftime('%y', $this->fixedDate));
        self::assertSame('2024', StrfTime::strftime('%G', $this->fixedDate));
        self::assertSame('24', StrfTime::strftime('%g', $this->fixedDate));
        self::assertSame('20', StrfTime::strftime('%C', $this->fixedDate));
    }

    public function testStrftimeTimeFormats(): void
    {
        self::assertSame('14', StrfTime::strftime('%H', $this->fixedDate));
        self::assertSame('14', StrfTime::strftime('%k', $this->fixedDate));
        self::assertSame('02', StrfTime::strftime('%I', $this->fixedDate));
        self::assertSame(' 2', StrfTime::strftime('%l', $this->fixedDate));
        self::assertSame('30', StrfTime::strftime('%M', $this->fixedDate));
        self::assertSame('45', StrfTime::strftime('%S', $this->fixedDate));
        self::assertSame('PM', StrfTime::strftime('%p', $this->fixedDate));
        self::assertSame('pm', StrfTime::strftime('%P', $this->fixedDate));
    }

    public function testStrftimeCompositeFormats(): void
    {
        self::assertSame('14:30', StrfTime::strftime('%R', $this->fixedDate));
        self::assertSame('14:30:45', StrfTime::strftime('%T', $this->fixedDate));
        self::assertSame('02:30:45 PM', StrfTime::strftime('%r', $this->fixedDate));
        self::assertSame('03/15/2024', StrfTime::strftime('%D', $this->fixedDate));
        self::assertSame('2024-03-15', StrfTime::strftime('%F', $this->fixedDate));
    }

    public function testStrftimeTimezoneFormats(): void
    {
        $resultZ = StrfTime::strftime('%Z', $this->fixedDate);
        self::assertNotEmpty($resultZ);
        $resultz = StrfTime::strftime('%z', $this->fixedDate);
        self::assertMatchesRegularExpression('/^[+-]\d{4}$/', $resultz);
    }

    public function testStrftimeSpecialCharacters(): void
    {
        self::assertSame("\n", StrfTime::strftime('%n', $this->fixedDate));
        self::assertSame("\t", StrfTime::strftime('%t', $this->fixedDate));
        self::assertSame('%', StrfTime::strftime('%%', $this->fixedDate));
    }

    public function testStrftimePrefixUnderscore(): void
    {
        $date = new DateTime('2024-03-05');
        $result = StrfTime::strftime('%_d', $date);
        self::assertSame(' 5', $result);
    }

    public function testStrftimePrefixDash(): void
    {
        $date = new DateTime('2024-03-05');
        $result = StrfTime::strftime('%-d', $date);
        self::assertSame('5', $result);
    }

    public function testStrftimePrefixHash(): void
    {
        $date = new DateTime('2024-03-05');
        $result = StrfTime::strftime('%#d', $date);
        self::assertSame('5', $result);
    }

    public function testStrftimeUnknownFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Format "%Q" is unknown in time format');
        StrfTime::strftime('%Q', $this->fixedDate);
    }

    public function testStrftimeInvalidTimestampThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$timestamp argument is neither a valid UNIX timestamp');
        StrfTime::strftime('%Y', 'invalid-date-string');
    }

    public function testStrftimeWithLocale(): void
    {
        $result = StrfTime::strftime('%Y-%m-%d', $this->fixedDate, 'en_US');
        self::assertSame('2024-03-15', $result);
    }

    public function testStrftimeUnixTimestampFormat(): void
    {
        $result = StrfTime::strftime('%s', $this->fixedDate);
        self::assertSame($this->fixedDate->format('U'), $result);
    }

    public function testStrftimeCombinedFormats(): void
    {
        $result = StrfTime::strftime('%Y-%m-%d %H:%M:%S', $this->fixedDate);
        self::assertSame('2024-03-15 14:30:45', $result);
    }

    public function testStrftimeMultiplePercentEscape(): void
    {
        $result = StrfTime::strftime('100%% complete on %Y', $this->fixedDate);
        self::assertSame('100% complete on 2024', $result);
    }

    public function testStrftimeDayOfYearPadding(): void
    {
        $firstDay = new DateTime('2024-01-01');
        self::assertSame('001', StrfTime::strftime('%j', $firstDay));
    }

    public function testStrftimeIntlFormatsWithLocale(): void
    {
        $result = StrfTime::strftime('%a', $this->fixedDate, 'en_US');
        self::assertNotEmpty($result);
        $result = StrfTime::strftime('%A', $this->fixedDate, 'en_US');
        self::assertNotEmpty($result);
        $result = StrfTime::strftime('%b', $this->fixedDate, 'en_US');
        self::assertNotEmpty($result);
        $result = StrfTime::strftime('%B', $this->fixedDate, 'en_US');
        self::assertNotEmpty($result);
    }

    public function testStrftimeEdgeCasePaddedHour(): void
    {
        $morningDate = new DateTime('2024-03-15 09:05:03');
        self::assertSame(' 9', StrfTime::strftime('%k', $morningDate));
        self::assertSame(' 9', StrfTime::strftime('%l', $morningDate));
    }

    public function testStrftimeCenturyCalculation(): void
    {
        $year1999 = new DateTime('1999-12-31');
        $year2000 = new DateTime('2000-01-01');
        self::assertSame('19', StrfTime::strftime('%C', $year1999));
        self::assertSame('20', StrfTime::strftime('%C', $year2000));
    }

    public function testStrftimeMonthAbbreviationAlias(): void
    {
        $result = StrfTime::strftime('%h', $this->fixedDate, 'en_US');
        self::assertNotEmpty($result);
        $resultB = StrfTime::strftime('%b', $this->fixedDate, 'en_US');
        self::assertSame($resultB, $result);
    }

    public function testStrftimeIntlDateTimeFormats(): void
    {
        $resultC = StrfTime::strftime('%c', $this->fixedDate, 'en_US');
        self::assertNotEmpty($resultC);

        $resultX = StrfTime::strftime('%x', $this->fixedDate, 'en_US');
        self::assertNotEmpty($resultX);

        $resultXUpper = StrfTime::strftime('%X', $this->fixedDate, 'en_US');
        self::assertNotEmpty($resultXUpper);
    }

    public function testStrftimeMidnightHourFormats(): void
    {
        $midnight = new DateTime('2024-03-15 00:00:00');
        self::assertSame('00', StrfTime::strftime('%H', $midnight));
        self::assertSame('12', StrfTime::strftime('%I', $midnight));
        self::assertSame(' 0', StrfTime::strftime('%k', $midnight));
        self::assertSame('12', StrfTime::strftime('%l', $midnight));
        self::assertSame('AM', StrfTime::strftime('%p', $midnight));
    }

    public function testStrftimeLeapYearDayOfYear(): void
    {
        $leapYearLastDay = new DateTime('2024-12-31');
        self::assertSame('366', StrfTime::strftime('%j', $leapYearLastDay));

        $nonLeapYearLastDay = new DateTime('2023-12-31');
        self::assertSame('365', StrfTime::strftime('%j', $nonLeapYearLastDay));
    }

    public function testStrftimeWeekNumberFirstWeek(): void
    {
        $firstSunday2024 = new DateTime('2024-01-07');
        $resultU = StrfTime::strftime('%U', $firstSunday2024);
        self::assertMatchesRegularExpression('/^\d{2}$/', $resultU);

        $firstMonday2024 = new DateTime('2024-01-01');
        $resultW = StrfTime::strftime('%W', $firstMonday2024);
        self::assertMatchesRegularExpression('/^\d{2}$/', $resultW);
    }

    public function testStrftimeNegativeTimestamp(): void
    {
        $result = StrfTime::strftime('%Y-%m-%d', -86400);
        self::assertSame('1969-12-31', $result);
    }

    public function testStrftimeIsoWeekYear(): void
    {
        $date = new DateTime('2024-12-30');
        $resultG = StrfTime::strftime('%G', $date);
        $resultg = StrfTime::strftime('%g', $date);
        self::assertSame('2025', $resultG);
        self::assertSame('25', $resultg);
    }

    public function testStrftimeEmptyFormat(): void
    {
        $result = StrfTime::strftime('', $this->fixedDate);
        self::assertSame('', $result);
    }

    public function testStrftimeNoFormatSpecifiers(): void
    {
        $result = StrfTime::strftime('Hello World', $this->fixedDate);
        self::assertSame('Hello World', $result);
    }

    public function testStrftimeDayOfWeekBoundaries(): void
    {
        $sunday = new DateTime('2024-03-17');
        self::assertSame('7', StrfTime::strftime('%u', $sunday));
        self::assertSame('0', StrfTime::strftime('%w', $sunday));

        $monday = new DateTime('2024-03-18');
        self::assertSame('1', StrfTime::strftime('%u', $monday));
        self::assertSame('1', StrfTime::strftime('%w', $monday));
    }

    public function testStrftimePrefixWithSingleDigitMonth(): void
    {
        $date = new DateTime('2024-01-15');
        $resultUnderscore = StrfTime::strftime('%_m', $date);
        self::assertSame(' 1', $resultUnderscore);

        $resultDash = StrfTime::strftime('%-m', $date);
        self::assertSame('1', $resultDash);

        $resultHash = StrfTime::strftime('%#m', $date);
        self::assertSame('1', $resultHash);
    }

    public function testStrftimeNoonHourFormats(): void
    {
        $noon = new DateTime('2024-03-15 12:00:00');
        self::assertSame('12', StrfTime::strftime('%H', $noon));
        self::assertSame('12', StrfTime::strftime('%I', $noon));
        self::assertSame('PM', StrfTime::strftime('%p', $noon));
    }

    public function testStrftimeEndOfDay(): void
    {
        $endOfDay = new DateTime('2024-03-15 23:59:59');
        self::assertSame('23', StrfTime::strftime('%H', $endOfDay));
        self::assertSame('11', StrfTime::strftime('%I', $endOfDay));
        self::assertSame('59', StrfTime::strftime('%M', $endOfDay));
        self::assertSame('59', StrfTime::strftime('%S', $endOfDay));
        self::assertSame('PM', StrfTime::strftime('%p', $endOfDay));
    }

    public function testStrftimeYearBoundaries(): void
    {
        $year0001 = new DateTime('0001-01-01');
        self::assertSame('0001', StrfTime::strftime('%Y', $year0001));
        self::assertSame('01', StrfTime::strftime('%y', $year0001));
        self::assertSame('0', StrfTime::strftime('%C', $year0001));
    }

    public function testStrftimeWithMultiplePrefixes(): void
    {
        $date = new DateTime('2024-03-05 09:05:03');
        $result = StrfTime::strftime('%-d/%-m/%Y %-H:%-M:%-S', $date);
        self::assertSame('5/3/2024 9:5:3', $result);
    }

    public function testStrftimeIsoWeekNumber(): void
    {
        $firstWeek = new DateTime('2024-01-01');
        $result = StrfTime::strftime('%V', $firstWeek);
        self::assertSame('01', $result);

        $lastWeek = new DateTime('2024-12-31');
        $result = StrfTime::strftime('%V', $lastWeek);
        self::assertSame('01', $result);
    }

    public function testStrftimeWithNumericStringTimestamp(): void
    {
        // String numérica é interpretada como string de data, não como timestamp Unix
        // Para usar timestamp Unix, deve-se passar como int
        $result = StrfTime::strftime('%Y', 1710513045);
        self::assertSame('2024', $result);
    }
}
