<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Compare;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    public function testDaysDifferenceBetweenData(): void
    {
        self::assertEquals('+30', Compare::daysDifferenceBetweenData('31/10/2020', '30/11/2020'));
        self::assertEquals('-19', Compare::daysDifferenceBetweenData('14/10/2020', '25/09/2020'));
    }

    public function testStartDateLessThanEnd(): void
    {
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd(null, '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', null));
        self::assertFalse(Compare::startDateLessThanEnd(null, null));
        self::assertTrue(Compare::startDateLessThanEnd('01/10/2020', '04/10/2020'));
        self::assertTrue(Compare::startDateLessThanEnd('15/10/2020', '15/10/2020'));
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
        self::assertEquals('36', Compare::calculateAgeInYears('17/04/1989'));
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

    public function testBeginUrlWith(): void
    {
        self::assertTrue(
            Compare::beginUrlWith('/teste', '/teste/variavel'),
            'Erro ao executar a função testBeginUrlWith!',
        );
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste1234'), 'Erro ao executar a função testBeginUrlWith!');
        self::assertNotTrue(
            Compare::beginUrlWith('/teste123', '/testeasc'),
            'Erro ao executar a função testBeginUrlWith!',
        );
    }

    public function testFinishUrlWith(): void
    {
        self::assertTrue(
            Compare::finishUrlWith('/teste', 'asd/teste'),
            'Erro ao executar a função testFinishUrlWith!',
        );
        self::assertTrue(
            Compare::finishUrlWith('/teste', 'sistema/teste'),
            'Erro ao executar a função testFinishUrlWith!',
        );
        self::assertNotTrue(
            Compare::finishUrlWith('/test', 'sistema/teste1'),
            'Erro ao executar a função testFinishUrlWith!',
        );
    }

    public function testCompareStringFrom(): void
    {
        self::assertTrue(
            Compare::compareStringFrom('sistema', 'sistema/teste', 0, 7),
            'Erro ao executar a função compareStringFrom!',
        );
    }

    public function testDaysDifferenceBetweenDataAmericanFormat(): void
    {
        self::assertEquals('+10', Compare::daysDifferenceBetweenData('2020-10-01', '2020-10-11'));
        self::assertEquals('-5', Compare::daysDifferenceBetweenData('2020-10-10', '2020-10-05'));
    }

    public function testDaysDifferenceBetweenDataSameDay(): void
    {
        self::assertEquals('+0', Compare::daysDifferenceBetweenData('15/10/2020', '15/10/2020'));
    }

    public function testStartDateLessThanEndEmptyStrings(): void
    {
        self::assertFalse(Compare::startDateLessThanEnd('', '24/10/2020'));
        self::assertFalse(Compare::startDateLessThanEnd('27/11/2020', ''));
        self::assertFalse(Compare::startDateLessThanEnd('', ''));
    }

    public function testStartHourLessThanEndEqual(): void
    {
        self::assertNull(Compare::startHourLessThanEnd('10:00:00', '10:00:00', 'Erro'));
    }

    public function testStartHourLessThanEndEmptySecondHour(): void
    {
        $msgVazio = 'Um ou mais campos horas não foram preenchidos!';
        self::assertEquals($msgVazio, Compare::startHourLessThanEnd('10:00:00', '', $msgVazio));
    }

    public function testCalculateAgeInYearsAmericanFormat(): void
    {
        $age = Compare::calculateAgeInYears('1989-04-17');
        self::assertSame(36, $age);
    }

    public function testDifferenceBetweenHoursZero(): void
    {
        self::assertEquals('00:00:00', Compare::differenceBetweenHours('10:00:00', '10:00:00'));
    }

    public function testDifferenceBetweenHoursPositive(): void
    {
        self::assertEquals('02:30:00', Compare::differenceBetweenHours('08:00:00', '10:30:00'));
    }

    public function testCheckDataEqualitySameCaseSensitive(): void
    {
        self::assertTrue(Compare::checkDataEquality('Teste', 'Teste'));
        self::assertTrue(Compare::checkDataEquality('', ''));
    }

    public function testContainsAtBeginning(): void
    {
        self::assertTrue(Compare::contains('Açafrão', 'Aça'));
    }

    public function testContainsAtEnd(): void
    {
        self::assertTrue(Compare::contains('Açafrão', 'rão'));
    }

    public function testContainsEmptySearch(): void
    {
        self::assertTrue(Compare::contains('Açafrão', ''));
    }

    public function testContainsEmptyValue(): void
    {
        self::assertFalse(Compare::contains('', 'teste'));
    }

    public function testCompareStringFromExactMatch(): void
    {
        self::assertTrue(Compare::compareStringFrom('sistema', 'sistema', 0, 7));
    }

    public function testCompareStringFromMiddlePosition(): void
    {
        self::assertTrue(Compare::compareStringFrom('teste', 'um_teste_aqui', 3, 5));
    }

    public function testCompareStringFromNoMatch(): void
    {
        self::assertFalse(Compare::compareStringFrom('xyz', 'abcdef', 0, 3));
    }

    public function testBeginUrlWithExactMatch(): void
    {
        self::assertTrue(Compare::beginUrlWith('/teste', '/teste'));
    }

    public function testBeginUrlWithCaseInsensitive(): void
    {
        self::assertTrue(Compare::beginUrlWith('/TESTE', '/teste/variavel'));
    }

    public function testFinishUrlWithExactMatch(): void
    {
        self::assertTrue(Compare::finishUrlWith('/teste', '/teste'));
    }

    public function testFinishUrlWithCaseInsensitive(): void
    {
        self::assertTrue(Compare::finishUrlWith('/TESTE', 'sistema/teste'));
    }
}
