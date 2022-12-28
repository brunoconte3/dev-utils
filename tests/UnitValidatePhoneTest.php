<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidatePhone;
use PHPUnit\Framework\TestCase;

class UnitValidatePhoneTest extends TestCase
{
    public function testValidate(): void
    {
        self::assertEquals(true, ValidatePhone::validate('44999999999'));
        self::assertEquals(false, ValidatePhone::validate('449999999'));
    }
}
