<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Compare;
use PHPUnit\Framework\TestCase;

class UnitTestCompare extends TestCase
{
    public function testDaysDifferenceBetweenData(): void
    {
        self::assertEquals('+30', Compare::daysDifferenceBetweenData('31/10/2020', '30/11/2020'));
        self::assertEquals('-19', Compare::daysDifferenceBetweenData('14/10/2020', '25/09/2020'));
    }

    public function testStartDateLessThanEnd(): void
    {
        self::assertFalse(Compare::startDateLessThanEnd('30/11/2020', '31/10/2020'));
        self::assertTrue(Compare::startDateLessThanEnd('31/10/2020', '30/11/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('31/10/2020', '31/10/2020'));
    }

    public function testStartHourLessThanEnd(): void
    {
        $msg = 'Hora Inicial não pode ser maior que a Hora Final!';
        $msgVazio = 'Um ou mais campos horas não foram preenchidos!';
        self::assertEquals($msg, Compare::startHourLessThanEnd('12:05:01', '10:20:01', $msg));
        self::assertEquals($msgVazio, Compare::startHourLessThanEnd('', '10:20:01', $msgVazio));
        self::assertNull(Compare::startHourLessThanEnd('10:05:01', '12:20:01', $msg));
    }

    public function testCalculateAgeInYears(): void
    {
        self::assertEquals('31', Compare::calculateAgeInYears('20/05/1989'));
        self::assertEquals('0', Compare::calculateAgeInYears('03/05/2020'));
    }

    public function testDifferenceBetweenHours(): void
    {
        self::assertEquals('01:36:28', Compare::differenceBetweenHours('10:41:55', '12:18:23'));
        self::assertEquals('-12:59:55', Compare::differenceBetweenHours('23:00:05', '12:00:00'));
    }

    public function testCheckDataEquality(): void
    {
        self::assertFalse(Compare::checkDataEquality('AçaFrão', 'Açafrão'));
        self::assertTrue(Compare::checkDataEquality('AçaFrão', 'Açafrão', false));
    }

    public function testContains(): void
    {
        self::assertFalse(Compare::contains('AçaFrão', 'Mac'));
        self::assertTrue(Compare::contains('AçaFrão', 'çaF'));
    }
}
