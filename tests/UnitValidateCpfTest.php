<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateCpf;
use PHPUnit\Framework\TestCase;

class UnitValidateCpfTest extends TestCase
{
    public function testValidate(): void
    {
        self::assertEquals(true, ValidateCpf::validateCpf('257.877.760-89'));
        self::assertEquals(false, ValidateCpf::validateCpf('257.877.700-88'));
    }
}
