<?php

declare(strict_types=1);

use DevUtils\CoverageGate;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'CoverageGate.php';

if ($argc !== 3) {
    fwrite(STDERR, 'Usage: ' . $argv[0] . " <path/to/index.xml> <threshold>\n");
    exit(1);
}

try {
    $threshold = CoverageGate::parseThreshold($argv[2]);
    $ratio = CoverageGate::readRatio($argv[1]);
} catch (Throwable $exception) {
    fwrite(STDERR, '[ERROR] ' . $exception->getMessage() . "\n");
    exit(1);
}

$message = CoverageGate::message($ratio, $threshold);

if (!CoverageGate::passes($ratio, $threshold)) {
    fwrite(STDERR, $message . "\n");
    exit(1);
}

echo $message . "\n";
