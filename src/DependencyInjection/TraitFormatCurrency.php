<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use InvalidArgumentException;

trait TraitFormatCurrency
{
    private static function formatCurrencyForFloat(float | int | string $value): float
    {
        if (!is_string($value)) {
            return (float) $value;
        }

        $trimmed = trim($value);
        $isNegative = str_starts_with($trimmed, '-');
        $separator = str_contains($trimmed, ',') ? ',' : '.';
        $valueParts = explode($separator, $trimmed);

        if (isset($valueParts[1]) && strlen($valueParts[1]) === 1) {
            $valueParts[1] .= '0';
        }

        $normalized = implode($separator, $valueParts);
        $onlyNumbers = self::onlyNumbers($normalized);
        $numericValue = $onlyNumbers !== '' ? $onlyNumbers : '000';

        if (preg_match('/[,.]/', substr(substr($normalized, -3), 0, 1)) === 1) {
            $numericValue = substr_replace($numericValue, '.', -2, 0);
        }

        return (float) ($isNegative ? "-$numericValue" : $numericValue);
    }

    private static function formatCurrency(
        float $value,
        string $decimalSeparator,
        string $thousandsSeparator,
        string $prefix = ''
    ): string {
        return $prefix . number_format($value, 2, $decimalSeparator, $thousandsSeparator);
    }

    private static function validateCurrencyValue(string $name, float | int | string $value): void
    {
        if (!is_string($value) || trim($value) === '' || preg_match('/\d/', $value) === 1) {
            return;
        }

        throw new InvalidArgumentException("$name precisa conter ao menos um número!");
    }

    public static function currency(float | int | string $value, string $coinType = ''): string
    {
        self::validateCurrencyValue('currency', $value);
        $normalizedValue = self::formatCurrencyForFloat($value);
        return self::formatCurrency($normalizedValue, ',', '.', $coinType);
    }

    public static function currencyUsd(float | int | string $value, string $coinType = ''): string
    {
        self::validateCurrencyValue('currencyUsd', $value);
        $normalizedValue = self::formatCurrencyForFloat($value);
        return self::formatCurrency($normalizedValue, '.', ',', $coinType);
    }

    private static function dotIsDecimalSeparator(string $value): bool
    {
        $lastDot = strrpos($value, '.');

        if ($lastDot === false || substr_count($value, '.') > 1) {
            return false;
        }

        $decimals = strlen($value) - $lastDot - 1;

        return $decimals === 1 || $decimals === 2;
    }

    public static function pointOnlyValue(string $value): string
    {
        $sanitized = (string) preg_replace('/[^0-9,.]/', '', $value);

        if (str_contains($sanitized, ',')) {
            $lastDot = strrpos($sanitized, '.');

            return $lastDot !== false && $lastDot > strrpos($sanitized, ',')
                ? str_replace(',', '', $sanitized)
                : str_replace(',', '.', str_replace('.', '', $sanitized));
        }

        return self::dotIsDecimalSeparator($sanitized) ? $sanitized : str_replace('.', '', $sanitized);
    }

    public static function writeCurrencyExtensive(float $numeral): string
    {
        if ($numeral <= 0) {
            throw new InvalidArgumentException('O valor numeral deve ser maior que zero!');
        }
        return parent::extensive($numeral);
    }
}
