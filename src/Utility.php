<?php

declare(strict_types=1);

namespace devUtils;

class Utility
{
    public static function captureClientIp(): ?string
    {
        if (!empty(filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP'))) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty(filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'))) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else {
            $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }
        return $ip;
    }
}
