<?php

declare(strict_types=1);

namespace DevUtils;

use RuntimeException;
use SimpleXMLElement;

final class CoverageGate
{
    public static function parseThreshold(string $value): float
    {
        if (!is_numeric($value)) {
            throw new RuntimeException("Threshold must be numeric, received '$value'.");
        }
        return (float) $value;
    }

    public static function readRatio(string $reportFile): float
    {
        if (!is_file($reportFile) || !is_readable($reportFile)) {
            throw new RuntimeException("Coverage report not found: '$reportFile'.");
        }

        $xml = self::loadXml($reportFile);
        if (!$xml instanceof SimpleXMLElement) {
            throw new RuntimeException("Coverage report is not valid XML: '$reportFile'.");
        }

        if (!isset($xml->project->directory->totals->lines['percent'])) {
            throw new RuntimeException("Coverage report has no line totals: '$reportFile'.");
        }

        return (float) (string) $xml->project->directory->totals->lines['percent'];
    }

    public static function passes(float $ratio, float $threshold): bool
    {
        return $ratio >= $threshold;
    }

    public static function message(float $ratio, float $threshold): string
    {
        $label = self::passes($ratio, $threshold) ? '[PASS]' : '[FAIL]';
        return "$label Code coverage is $ratio% (required minimum is $threshold%).";
    }

    private static function loadXml(string $reportFile): SimpleXMLElement | false
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($reportFile);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $xml;
    }
}
