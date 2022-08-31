<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateHour;
use PHPUnit\Framework\TestCase;

class UnitTestValidateHour extends TestCase
{
    public function testValidateHour(): void
    {
        self::assertTrue(ValidateHour::validateHour('08:50'));
        self::assertFalse(ValidateHour::validateHour('08:5'));
    }
}
