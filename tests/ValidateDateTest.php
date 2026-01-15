<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DateInterval;
use DateTimeImmutable;
use DevUtils\Format;
use DevUtils\ValidateDate;
use PHPUnit\Framework\TestCase;

class ValidateDateTest extends TestCase
{
    private const UTC_DATETIME_WITH_TIMEZONE = '2025-11-20T10:30:00+00:00';
    private const UTC_DATETIME_WITH_MILLISECONDS = '2025-11-20T10:30:00.123+00:00';
    private const UTC_DATETIME_WITHOUT_TIMEZONE = '2025-11-20T10:30:00';

    private static function biuldDataTestDateIso8601Z(): array
    {
        return [
            '2025-11-20T10:30:00Z',
            self::UTC_DATETIME_WITH_TIMEZONE,
            '2025-11-20T10:30:00-03:00',
            '2025-11-20T10:30:00+03:30',
            '2025-11-20T10:30:00.123Z',
            self::UTC_DATETIME_WITH_MILLISECONDS,
            '2025-11-20T10:30:00.123456789Z',
            '2025-11-20T10:30:00.1234567Z',
            '20251120T103000Z',
            '2025-11-20T10:30Z',
        ];
    }

    private static function biuldDataDateIsoTest(): array
    {
        return [
            '2025',
            '2025-11',
            '2025-11-20',
            '20251120',
            '2025-11-20T10:30',
            '2025-11-20T10:30+03:00',
            self::UTC_DATETIME_WITHOUT_TIMEZONE,
            self::UTC_DATETIME_WITH_TIMEZONE,
            '2025-11-20T10:30:00-03:00',
            '2025-11-20T10:30:00+03:30',
            '2025-11-20T10:30:00.123',
            self::UTC_DATETIME_WITH_MILLISECONDS,
            '20251120T103000',
            '20251120T103000+00:00',
            '2025-W48',
            '2025-W48-1',
            '2025-324',
            ...self::biuldDataTestDateIso8601Z(),
        ];
    }

    public function testValidateDateBrazil(): void
    {
        self::assertEquals(true, ValidateDate::validateDateBrazil('29/04/2021'));
        self::assertEquals(false, ValidateDate::validateDateBrazil('31/04/2021'));
    }

    public function testValidateDateAmerican(): void
    {
        self::assertEquals(true, ValidateDate::validateDateAmerican('2021-04-29'));
        self::assertEquals(false, ValidateDate::validateDateAmerican('2021-04-31'));
    }

    public function testValidateTimeStamp(): void
    {
        self::assertEquals(true, ValidateDate::validateTimeStamp('2021-04-29 11:17:12'));
        self::assertEquals(false, ValidateDate::validateTimeStamp('2021-04-31 11:1'));
    }

    public function testValidateDateNotFuture(): void
    {
        $dateNow = new DateTimeImmutable();
        $newDateFuture = $dateNow->add(new DateInterval('P32D'))->format('Y-m-d');
        self::assertEquals(true, ValidateDate::validateDateNotFuture(Format::dateAmerican('28/12/2022')));
        self::assertEquals(false, ValidateDate::validateDateNotFuture($newDateFuture));
    }

    public function testValidateDateUTCWithoutTimezone(): void
    {
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone(self::UTC_DATETIME_WITHOUT_TIMEZONE));
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone('1999-01-01T00:00:00'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20 10:30:00'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30:00Z'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone(self::UTC_DATETIME_WITH_TIMEZONE));
    }

    public function testValidateDateIso8601AllFormats(): void
    {
        $valid = self::biuldDataDateIsoTest();
        foreach ($valid as $v) {
            self::assertTrue(ValidateDate::validateDateIso8601($v), "Falhou para: $v");
        }
        $invalid = [
            '2025-13-40',
            '2025-11-20T25:61:00Z',
            '2025-11-20T10:30:00+99:99',
            '2025-11-20 10:30:00Z',
            '2025-W99',
            '2025-400',
        ];
        foreach ($invalid as $v) {
            self::assertFalse(ValidateDate::validateDateIso8601($v), "Aceitou inválido: $v");
        }
    }

    public function testValidateDateBrazilWithInvalidFormats(): void
    {
        self::assertFalse(ValidateDate::validateDateBrazil(''));
        self::assertFalse(ValidateDate::validateDateBrazil('29-04-2021'));
        self::assertFalse(ValidateDate::validateDateBrazil('2021/04/29'));
        self::assertFalse(ValidateDate::validateDateBrazil('29/04'));
        self::assertFalse(ValidateDate::validateDateBrazil('29/04/21'));
    }

    public function testValidateDateBrazilLeapYear(): void
    {
        self::assertTrue(ValidateDate::validateDateBrazil('29/02/2024'));
        self::assertFalse(ValidateDate::validateDateBrazil('29/02/2023'));
    }

    public function testValidateDateBrazilEdgeDays(): void
    {
        self::assertTrue(ValidateDate::validateDateBrazil('01/01/2021'));
        self::assertTrue(ValidateDate::validateDateBrazil('31/12/2021'));
        self::assertTrue(ValidateDate::validateDateBrazil('31/01/2021'));
        self::assertFalse(ValidateDate::validateDateBrazil('32/01/2021'));
        self::assertFalse(ValidateDate::validateDateBrazil('00/01/2021'));
    }

    public function testValidateDateAmericanWithInvalidFormats(): void
    {
        self::assertFalse(ValidateDate::validateDateAmerican(''));
        self::assertFalse(ValidateDate::validateDateAmerican('2021/04/29'));
        self::assertFalse(ValidateDate::validateDateAmerican('29-04-2021'));
        self::assertFalse(ValidateDate::validateDateAmerican('2021-04'));
        self::assertFalse(ValidateDate::validateDateAmerican('21-04-29'));
    }

    public function testValidateDateAmericanLeapYear(): void
    {
        self::assertTrue(ValidateDate::validateDateAmerican('2024-02-29'));
        self::assertFalse(ValidateDate::validateDateAmerican('2023-02-29'));
    }

    public function testValidateDateAmericanEdgeDays(): void
    {
        self::assertTrue(ValidateDate::validateDateAmerican('2021-01-01'));
        self::assertTrue(ValidateDate::validateDateAmerican('2021-12-31'));
        self::assertFalse(ValidateDate::validateDateAmerican('2021-01-32'));
        self::assertFalse(ValidateDate::validateDateAmerican('2021-00-15'));
        self::assertFalse(ValidateDate::validateDateAmerican('2021-13-15'));
    }

    public function testValidateTimeStampWithInvalidFormats(): void
    {
        self::assertFalse(ValidateDate::validateTimeStamp(''));
        self::assertFalse(ValidateDate::validateTimeStamp('2021-04-29'));
        self::assertFalse(ValidateDate::validateTimeStamp('2021-04-29 11:17'));
        self::assertFalse(ValidateDate::validateTimeStamp('29/04/2021'));
    }

    public function testValidateTimeStampBrazilFormat(): void
    {
        self::assertTrue(ValidateDate::validateTimeStamp('29/04/2021 11:17:12'));
        self::assertFalse(ValidateDate::validateTimeStamp('29/04/2021 25:17:12'));
    }

    public function testValidateTimeStampEdgeTimes(): void
    {
        self::assertTrue(ValidateDate::validateTimeStamp('2021-04-29 00:00:00'));
        self::assertTrue(ValidateDate::validateTimeStamp('2021-04-29 23:59:59'));
        self::assertFalse(ValidateDate::validateTimeStamp('2021-04-29 24:00:00'));
        self::assertFalse(ValidateDate::validateTimeStamp('2021-04-29 11:60:00'));
        self::assertFalse(ValidateDate::validateTimeStamp('2021-04-29 11:17:60'));
    }

    public function testValidateDateNotFutureWithInvalidDate(): void
    {
        self::assertFalse(ValidateDate::validateDateNotFuture('invalid-date'));
    }

    public function testValidateDateNotFutureWithEmptyString(): void
    {
        self::assertTrue(ValidateDate::validateDateNotFuture(''));
    }

    public function testValidateDateNotFutureWithPastDates(): void
    {
        self::assertTrue(ValidateDate::validateDateNotFuture('2020-01-01'));
        self::assertTrue(ValidateDate::validateDateNotFuture('1990-06-15'));
        self::assertTrue(ValidateDate::validateDateNotFuture('2000-12-31'));
    }

    public function testValidateDateNotFutureWithToday(): void
    {
        $today = (new DateTimeImmutable())->format('Y-m-d');
        self::assertTrue(ValidateDate::validateDateNotFuture($today));
    }

    public function testValidateDateUTCWithoutTimezoneEdgeCases(): void
    {
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone('2000-01-01T00:00:00'));
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone('2099-12-31T23:59:59'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone(''));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20'));
    }

    public function testValidateDateIso8601Empty(): void
    {
        self::assertFalse(ValidateDate::validateDateIso8601(''));
    }

    public function testValidateDateIso8601Duration(): void
    {
        self::assertTrue(ValidateDate::validateDateIso8601('P1Y'));
        self::assertTrue(ValidateDate::validateDateIso8601('P1M'));
        self::assertTrue(ValidateDate::validateDateIso8601('P1D'));
        self::assertTrue(ValidateDate::validateDateIso8601('PT1H'));
        self::assertTrue(ValidateDate::validateDateIso8601('PT1M'));
        self::assertTrue(ValidateDate::validateDateIso8601('PT1S'));
        self::assertTrue(ValidateDate::validateDateIso8601('P1Y2M3DT4H5M6S'));
        self::assertFalse(ValidateDate::validateDateIso8601('P'));
    }

    public function testValidateDateIso8601WeekDate(): void
    {
        self::assertTrue(ValidateDate::validateDateIso8601('2025-W01'));
        self::assertTrue(ValidateDate::validateDateIso8601('2025-W52'));
        self::assertTrue(ValidateDate::validateDateIso8601('2025-W01-1'));
        self::assertTrue(ValidateDate::validateDateIso8601('2025-W01-7'));
        self::assertFalse(ValidateDate::validateDateIso8601('2025-W00'));
        self::assertFalse(ValidateDate::validateDateIso8601('2025-W54'));
    }

    public function testValidateDateIso8601OrdinalDate(): void
    {
        self::assertTrue(ValidateDate::validateDateIso8601('2025-001'));
        self::assertTrue(ValidateDate::validateDateIso8601('2025-365'));
        self::assertTrue(ValidateDate::validateDateIso8601('2024-366'));
        self::assertFalse(ValidateDate::validateDateIso8601('2025-000'));
        self::assertFalse(ValidateDate::validateDateIso8601('2025-366'));
    }

    public function testValidateDateIso8601Interval(): void
    {
        self::assertTrue(ValidateDate::validateDateIso8601('2025-01-01/2025-12-31'));
        self::assertTrue(ValidateDate::validateDateIso8601('P1Y/2025-12-31'));
        self::assertFalse(ValidateDate::validateDateIso8601('2025-01-01/'));
        self::assertFalse(ValidateDate::validateDateIso8601('/2025-12-31'));
    }
}
