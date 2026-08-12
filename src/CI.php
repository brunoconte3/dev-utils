<?php

declare(strict_types=1);

use DevUtils\CoverageGate;

// 1) rodando dentro do próprio repo:      <repo>/vendor/autoload.php
// 2) instalado como dependência, onde este arquivo fica em vendor/brunoconte3/dev-utils/src/:
//    <projeto>/vendor/autoload.php
$autoloadPaths = [
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
    dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (is_file($autoloadPath)) {
        require_once $autoloadPath; // NOSONAR - script de entrada CLI precisa carregar o autoloader
        break;
    }
}

if (!class_exists(CoverageGate::class)) {
    fwrite(STDERR, "[ERROR] Autoloader not found, run 'composer install' before using this script.\n");
    exit(1);
}

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
