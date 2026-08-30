<?php

declare(strict_types=1);

namespace DevUtils\Test\data;

use PHPUnit\Framework\TestCase;

class UnitCiTest extends TestCase
{
    private const OUTPUT_PASS = '[PASS]';

    private string $script = '';
    private string $directory = '';
    private string $report = '';
    private int $exitCode = 0;

    protected function setUp(): void
    {
        $this->script = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'CI.php';
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'devutils-ci-' . bin2hex(random_bytes(8));
        mkdir($this->directory);

        $this->report = $this->directory . DIRECTORY_SEPARATOR . 'index.xml';
        file_put_contents($this->report, $this->reportXml());
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    private function reportXml(): string
    {
        return <<<XML
        <?xml version="1.0"?>
        <phpunit xmlns="https://schema.phpunit.de/coverage/1.0">
          <project source="/app">
            <directory name="/">
              <totals>
                <lines total="100" executable="100" executed="90" percent="90.00"/>
              </totals>
            </directory>
          </project>
        </phpunit>
        XML;
    }

    private function runScript(string $arguments = ''): string
    {
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($this->script);
        $output = [];

        exec(trim($command . ' ' . $arguments) . ' 2>&1', $output, $exitCode);
        $this->exitCode = $exitCode;

        return implode("\n", $output);
    }

    public function testCiScriptIsReadable(): void
    {
        self::assertFileIsReadable($this->script);
    }

    public function testPassesWhenCoverageMeetsThreshold(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 80');

        self::assertStringContainsString(self::OUTPUT_PASS, $result);
        self::assertStringContainsString('90%', $result);
        self::assertSame(0, $this->exitCode);
    }

    public function testPassesWhenCoverageEqualsThreshold(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 90');

        self::assertStringContainsString(self::OUTPUT_PASS, $result);
        self::assertSame(0, $this->exitCode);
    }

    public function testPassesWhenThresholdIsZero(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 0');

        self::assertStringContainsString(self::OUTPUT_PASS, $result);
        self::assertSame(0, $this->exitCode);
    }

    public function testPassesWithFloatThreshold(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 50.5');

        self::assertStringContainsString(self::OUTPUT_PASS, $result);
        self::assertSame(0, $this->exitCode);
    }

    public function testFailsWhenCoverageIsBelowThreshold(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 100');

        self::assertStringContainsString('[FAIL]', $result);
        self::assertSame(1, $this->exitCode);
    }

    public function testFailureDoesNotLeakStackTrace(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' 100');

        self::assertStringNotContainsString('Stack trace', $result);
        self::assertStringNotContainsString('Uncaught', $result);
    }

    public function testFailsWithoutArguments(): void
    {
        $result = $this->runScript();

        self::assertStringContainsString('Usage:', $result);
        self::assertSame(1, $this->exitCode);
    }

    public function testFailsWithOnlyOneArgument(): void
    {
        $result = $this->runScript(escapeshellarg($this->report));

        self::assertStringContainsString('Usage:', $result);
        self::assertSame(1, $this->exitCode);
    }

    public function testReportsMissingFileInsteadOfZeroCoverage(): void
    {
        $missing = $this->directory . DIRECTORY_SEPARATOR . 'nonexistent.xml';

        $result = $this->runScript(escapeshellarg($missing) . ' 80');

        self::assertStringContainsString('Coverage report not found', $result);
        self::assertStringNotContainsString('[FAIL]', $result);
        self::assertSame(1, $this->exitCode);
    }

    public function testRejectsNonNumericThreshold(): void
    {
        $result = $this->runScript(escapeshellarg($this->report) . ' abc');

        self::assertStringContainsString('Threshold must be numeric', $result);
        self::assertStringNotContainsString(self::OUTPUT_PASS, $result);
        self::assertSame(1, $this->exitCode);
    }

    public function testRejectsMissingFileEvenWhenThresholdIsZero(): void
    {
        $missing = $this->directory . DIRECTORY_SEPARATOR . 'nonexistent.xml';

        $result = $this->runScript(escapeshellarg($missing) . ' 0');

        self::assertStringContainsString('Coverage report not found', $result);
        self::assertStringNotContainsString(self::OUTPUT_PASS, $result);
        self::assertSame(1, $this->exitCode);
    }
}
