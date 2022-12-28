<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateString;
use PHPUnit\Framework\TestCase;

class UnitValidateStringTest extends TestCase
{
    public function testMaxWords(): void
    {
        self::assertTrue(ValidateString::maxWords('Bruno Conte', 2));
        self::assertFalse(ValidateString::maxWords('Bruno Conte', 1));
    }

    public function testMinWords(): void
    {
        self::assertTrue(ValidateString::minWords('Bruno Conte', 2));
        self::assertFalse(ValidateString::minWords('Bruno Conte', 3));
    }
}
