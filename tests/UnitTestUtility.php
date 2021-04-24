<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Utility;
use PHPUnit\Framework\TestCase;

class UnitTestUtility extends TestCase
{
    public function testCaptureClientIp(): void
    {
        $ip = Utility::captureClientIp();
        $this->assertNull($ip); //Phpunit not read global ambient
    }
}
