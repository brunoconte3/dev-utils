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
}
