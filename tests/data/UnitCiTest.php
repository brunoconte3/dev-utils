<?php

declare(strict_types=1);

namespace DevUtils\Test\data;

use PHPUnit\Framework\TestCase;

class UnitCiTest extends TestCase
{
    private const CI_SCRIPT = './src/CI.php';
    private const COVERAGE_FILE = 'coverage/index.xml';

    public function testCiFileExists(): void
    {
        $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'CI.php';
        self::assertFileIsReadable($file);
    }

    public function testCiPassWithValidCoverage(): void
    {
        if (!file_exists(self::COVERAGE_FILE)) {
            self::markTestSkipped('Coverage file not found');
        }
        $returnCheck = shell_exec('php ' . self::CI_SCRIPT . ' ' . self::COVERAGE_FILE . ' 80');
        self::assertStringContainsString('[PASS]', ($returnCheck ?: ''));
    }

    public function testCiPassWithZeroThreshold(): void
    {
        if (!file_exists(self::COVERAGE_FILE)) {
            self::markTestSkipped('Coverage file not found');
        }
        $returnCheck = shell_exec('php ' . self::CI_SCRIPT . ' ' . self::COVERAGE_FILE . ' 0');
        self::assertStringContainsString('[PASS]', ($returnCheck ?: ''));
    }

    public function testCiFailWithHighThreshold(): void
    {
        if (!file_exists(self::COVERAGE_FILE)) {
            self::markTestSkipped('Coverage file not found');
        }
        exec('php ' . self::CI_SCRIPT . ' ' . self::COVERAGE_FILE . ' 100.1 2>&1', $output, $exitCode);
        $result = implode("\n", $output);
        self::assertStringContainsString('[FAIL]', $result);
        self::assertNotEquals(0, $exitCode);
    }

    public function testCiWithoutArguments(): void
    {
        exec('php ' . self::CI_SCRIPT . ' 2>&1', $output, $exitCode);
        $result = implode("\n", $output);
        self::assertStringContainsString('Usage:', $result);
        self::assertEquals(255, $exitCode);
    }

    public function testCiWithOnlyOneArgument(): void
    {
        exec('php ' . self::CI_SCRIPT . ' ' . self::COVERAGE_FILE . ' 2>&1', $output, $exitCode);
        $result = implode("\n", $output);
        self::assertStringContainsString('Usage:', $result);
        self::assertEquals(255, $exitCode);
    }

    public function testCiWithInvalidFile(): void
    {
        exec('php ' . self::CI_SCRIPT . ' nonexistent.xml 80 2>&1', $output, $exitCode);
        self::assertNotEquals(0, $exitCode);
    }

    public function testCiWithFloatThreshold(): void
    {
        if (!file_exists(self::COVERAGE_FILE)) {
            self::markTestSkipped('Coverage file not found');
        }
        $returnCheck = shell_exec('php ' . self::CI_SCRIPT . ' ' . self::COVERAGE_FILE . ' 50.5');
        self::assertStringContainsString('[PASS]', ($returnCheck ?: ''));
    }
}
