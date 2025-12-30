<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DateInterval;
use DateTimeImmutable;
use DevUtils\Format;
use DevUtils\ValidateDate;
use PHPUnit\Framework\TestCase;

class UnitValidateDateTest extends TestCase
{
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
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30:00'));
        self::assertTrue(ValidateDate::validateDateUTCWithoutTimezone('1999-01-01T00:00:00'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20 10:30:00'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30:00Z'));
        self::assertFalse(ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30:00+00:00'));
    }

    private static function biuldDataTestDateIso8601Z(): array
    {
        return [
            '2025-11-20T10:30:00Z',
            '2025-11-20T10:30:00+00:00',
            '2025-11-20T10:30:00-03:00',
            '2025-11-20T10:30:00+03:30',
            '2025-11-20T10:30:00.123Z',
            '2025-11-20T10:30:00.123+00:00',
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
            '2025-11-20T10:30:00',
            '2025-11-20T10:30:00+00:00',
            '2025-11-20T10:30:00-03:00',
            '2025-11-20T10:30:00+03:30',
            '2025-11-20T10:30:00.123',
            '2025-11-20T10:30:00.123+00:00',
            '20251120T103000',
            '20251120T103000+00:00',
            '2025-W48',
            '2025-W48-1',
            '2025-324',
            ...self::biuldDataTestDateIso8601Z(),
        ];
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
}
