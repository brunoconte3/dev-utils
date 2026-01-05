<?php

if ($argc !== 3) {
    echo "Usage: " . $argv[0] . " <path/to/index.xml> <threshold>\n";
    exit(-1);
}

$file = $argv[1];
$threshold = (float) $argv[2];

$coverage = simplexml_load_file($file);
$ratio = !empty($coverage) ? (float) $coverage->project->directory->totals->lines["percent"] : 0;

if ($ratio < $threshold) {
    throw new InvalidArgumentException("[FAIL] Code coverage is $ratio% (required minimum is $threshold%)");
}

echo "[PASS] Code coverage is $ratio% (required minimum is $threshold%).";
