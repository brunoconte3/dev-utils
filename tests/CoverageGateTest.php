<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\CoverageGate;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CoverageGateTest extends TestCase
{
    private string $directory = '';

    protected function setUp(): void
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'devutils-gate-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = $directory;
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    private function writeReport(string $content): string
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'index.xml';
        file_put_contents($path, $content);
        return $path;
    }

    private function reportWithPercent(string $percent): string
    {
        return <<<XML
        <?xml version="1.0"?>
        <phpunit xmlns="https://schema.phpunit.de/coverage/1.0">
          <project source="/app">
            <directory name="/">
              <totals>
                <lines total="100" executable="100" executed="90" percent="$percent"/>
              </totals>
            </directory>
            <directory name="src">
              <totals>
                <lines total="50" executable="50" executed="10" percent="20.00"/>
              </totals>
            </directory>
          </project>
        </phpunit>
        XML;
    }

    public function testReadRatioReturnsProjectWidePercentage(): void
    {
        $report = $this->writeReport($this->reportWithPercent('92.86'));

        self::assertSame(92.86, CoverageGate::readRatio($report));
    }

    public function testReadRatioIgnoresPercentageOfNestedDirectories(): void
    {
        $report = $this->writeReport($this->reportWithPercent('100.00'));

        self::assertNotSame(20.0, CoverageGate::readRatio($report));
        self::assertSame(100.0, CoverageGate::readRatio($report));
    }

    public function testReadRatioThrowsWhenReportDoesNotExist(): void
    {
        $report = $this->directory . DIRECTORY_SEPARATOR . 'nonexistent.xml';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Coverage report not found: '$report'.");

        CoverageGate::readRatio($report);
    }

    public function testReadRatioThrowsWhenPathIsADirectory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Coverage report not found');

        CoverageGate::readRatio($this->directory);
    }

    public function testReadRatioThrowsWhenReportIsNotValidXml(): void
    {
        $report = $this->writeReport('<phpunit><project><directory>');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Coverage report is not valid XML');

        CoverageGate::readRatio($report);
    }

    public function testReadRatioThrowsWhenReportIsEmpty(): void
    {
        $report = $this->writeReport('');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Coverage report is not valid XML');

        CoverageGate::readRatio($report);
    }

    public function testReadRatioThrowsWhenReportHasNoLineTotals(): void
    {
        $report = $this->writeReport('<?xml version="1.0"?><phpunit><project><directory/></project></phpunit>');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Coverage report has no line totals');

        CoverageGate::readRatio($report);
    }

    public function testParseThresholdAcceptsIntegerString(): void
    {
        self::assertSame(80.0, CoverageGate::parseThreshold('80'));
    }

    public function testParseThresholdAcceptsFloatString(): void
    {
        self::assertSame(92.86, CoverageGate::parseThreshold('92.86'));
    }

    public function testParseThresholdAcceptsZero(): void
    {
        self::assertSame(0.0, CoverageGate::parseThreshold('0'));
    }

    public function testParseThresholdThrowsOnNonNumericValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Threshold must be numeric, received 'abc'.");

        CoverageGate::parseThreshold('abc');
    }

    public function testParseThresholdThrowsOnEmptyValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Threshold must be numeric');

        CoverageGate::parseThreshold('');
    }

    public function testPassesWhenRatioIsAboveThreshold(): void
    {
        self::assertTrue(CoverageGate::passes(92.86, 80.0));
    }

    public function testPassesWhenRatioEqualsThreshold(): void
    {
        self::assertTrue(CoverageGate::passes(80.0, 80.0));
    }

    public function testDoesNotPassWhenRatioIsBelowThreshold(): void
    {
        self::assertFalse(CoverageGate::passes(79.99, 80.0));
    }

    public function testMessageReportsPassLabelAndBothPercentages(): void
    {
        self::assertSame(
            '[PASS] Code coverage is 92.86% (required minimum is 80%).',
            CoverageGate::message(92.86, 80.0),
        );
    }

    public function testMessageReportsFailLabelAndBothPercentages(): void
    {
        self::assertSame(
            '[FAIL] Code coverage is 70.5% (required minimum is 80.25%).',
            CoverageGate::message(70.5, 80.25),
        );
    }
}
