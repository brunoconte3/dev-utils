<?php

declare(strict_types=1);

$urlHost = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
$url = filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI');

require_once 'confDefine.php';
