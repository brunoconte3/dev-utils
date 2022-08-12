<?php

declare(strict_types=1);

namespace DevUtils\Test;

use PHPUnit\Framework\TestCase;

class UnitTestCI extends TestCase
{
    public function testCi(): void
    {
        $returnCheck = shell_exec('php ./src/CI.php  coverage/index.xml 80');
        self::assertStringContainsString('[PASS]', $returnCheck);
    }
}
