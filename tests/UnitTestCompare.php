<?php

declare(strict_types=1);

namespace devUtils\test;

use devUtils\Compare;
use PHPUnit\Framework\TestCase;

class UnitTestCompare extends TestCase
{
    public function testDaysDifferenceBetweenData(): void
    {
        $this->assertEquals('+30', Compare::daysDifferenceBetweenData('31/10/2020', '30/11/2020'));
        $this->assertEquals('-19', Compare::daysDifferenceBetweenData('14/10/2020', '25/09/2020'));
    }

    public function testStartDateLessThanEnd(): void
    {
        $this->assertFalse(Compare::startDateLessThanEnd('30/11/2020', '31/10/2020'));
        $this->assertTrue(Compare::startDateLessThanEnd('31/10/2020', '30/11/2020'));
    }

    public function testStartHourLessThanEnd(): void
    {
        $msg = 'Hora Inicial não pode ser maior que a Hora Final!';
        $msgVazio = 'Um ou mais campos horas não foram preenchidos!';
        $this->assertEquals($msg, Compare::startHourLessThanEnd('12:05:01', '10:20:01', $msg));
        $this->assertEquals($msgVazio, Compare::startHourLessThanEnd('', '10:20:01', $msgVazio));
        $this->assertNull(Compare::startHourLessThanEnd('10:05:01', '12:20:01', $msg));
    }

    public function testCalculateAgeInYears(): void
    {
        $this->assertEquals('31', Compare::calculateAgeInYears('20/05/1989'));
        $this->assertEquals('0', Compare::calculateAgeInYears('03/05/2020'));
    }

    public function testDifferenceBetweenHours(): void
    {
        $this->assertEquals('01:36:28', Compare::differenceBetweenHours('10:41:55', '12:18:23'));
        $this->assertEquals('-12:59:55', Compare::differenceBetweenHours('23:00:05', '12:00:00'));
    }

    public function testCheckDataEquality(): void
    {
        $this->assertFalse(Compare::checkDataEquality('AçaFrão', 'Açafrão'));
        $this->assertTrue(Compare::checkDataEquality('AçaFrão', 'Açafrão', false));
    }

    public function testContains(): void
    {
        $this->assertFalse(Compare::contains('AçaFrão', 'Mac'));
        $this->assertTrue(Compare::contains('AçaFrão', 'çaF'));
    }
}
