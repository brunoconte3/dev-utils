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

    public static function currency(float | int | string $value, string $coinType = ''): string
    {
        $normalizedValue = self::formatCurrencyForFloat($value);
        return self::formatCurrency($normalizedValue, ',', '.', $coinType);
    }

    public static function currencyUsd(float | int | string $value, string $coinType = ''): string
    {
        $normalizedValue = self::formatCurrencyForFloat($value);
        return self::formatCurrency($normalizedValue, '.', ',', $coinType);
    }

    public static function pointOnlyValue(string $value): string
    {
        return str_replace(',', '.', preg_replace('/[^0-9,]/', '', $value) ?? '');
    }

    public static function writeCurrencyExtensive(float $numeral): string
    {
        if ($numeral <= 0) {
            throw new InvalidArgumentException('O valor numeral deve ser maior que zero!');
        }
        return parent::extensive($numeral);
    }
}
