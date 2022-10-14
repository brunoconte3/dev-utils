<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateCnpj;
use PHPUnit\Framework\TestCase;

class UnitValidateCnpjTest extends TestCase
{
    public function testValidate(): void
    {
        self::assertEquals(true, ValidateCnpj::validateCnpj('57.169.078/0001-51'));
        self::assertEquals(false, ValidateCnpj::validateCnpj('55.569.078/0001-51'));
    }
}
