<?php

declare(strict_types=1);

namespace DevUtils;

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

    public static function generatePassword(
        int $size,
        bool $uppercase = true,
        bool $lowercase = true,
        bool $numbers = true,
        bool $symbols = true
    ): string {
        $alphabet = 'abcdefghijklmnopqrstuvyxwz';
        $nums = '0123456789';
        $sym = '@#$!()-+%=';
        $password = null;

        if ($numbers) {
            $password .= str_shuffle($nums);
        }
        if ($symbols) {
            $password .= str_shuffle($sym);
        }
        if ($uppercase) {
            $password .= str_shuffle(strtoupper($alphabet));
        }
        if ($lowercase) {
            $password .= str_shuffle(strtolower($alphabet));
        }
        $result = substr(str_shuffle($password), 0, $size);
        if ($symbols && $size >= 10 && !boolval(preg_match('@[0-9]@', $result))) {
            $result = self::generatePassword($size, $uppercase, $lowercase, $numbers, $symbols);
        }
        return $result;
    }

    /*
    * @return string -> Cadeia URL completa
    * @param string $host -> Dominio do sistema
    * @param string $absolutePath -> Caminho absoluto
    * @param string $https -> 'on' para gerar url https, outro valor, gera url http
    */
    public static function buildUrl(string $host, string $absolutePath = '', string $https = null): string
    {
        $protocol = ((isset($https) && ($https === 'on')) ? 'https' : 'http');
        return $protocol . '://' . $host . $absolutePath;
    }
}
