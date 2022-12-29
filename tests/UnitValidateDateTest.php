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
}
