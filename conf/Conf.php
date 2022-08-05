<?php

declare(strict_types=1);

namespace DevUtils;

final class Config
{
    public function __construct()
    {
        define('URL_HOST', filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL));
        define('URL', filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI'));
    }
}
