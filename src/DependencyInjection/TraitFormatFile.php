<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

use DevUtils\ValidateFile;

trait TraitFormatFile
{
    private static function formatFileName(string $fileName = ''): string
    {
        $parts = explode('.', trim($fileName));
        $extension = end($parts);

        if (count($parts) > 1) {
            unset($parts[count($parts) - 1]);
        }

        $baseName = implode('_', $parts);
        $withoutSpecialChars = self::removeSpecialCharacters($baseName) ?? '';
        $normalized = preg_replace('/\W/', '_', strtolower($withoutSpecialChars));

        return "{$normalized}.{$extension}";
    }

    private static function generateFileName(?string $nameFile): string
    {
        $randomPart = random_int(0, PHP_INT_MAX) . random_int(0, PHP_INT_MAX) . random_int(0, PHP_INT_MAX) . time();
        return date('d-m-Y_s_') . uniqid($randomPart) . '_' . $nameFile;
    }

    private static function fileEntryValue(array $file, string $index, int | string $key, mixed $default): mixed
    {
        return is_array($file[$index]) && isset($file[$index][$key]) ? $file[$index][$key] : $default;
    }

    public static function restructFileArray(array $file = []): array
    {
        if (empty($file)) {
            return [];
        }

        $fileError = ValidateFile::validateFileErrorPhp($file);
        if (!empty($fileError)) {
            return $fileError;
        }

        if (!isset($file['name']) || !is_array($file['name'])) {
            return [];
        }

        $restructuredFiles = [];
        foreach ($file['name'] as $key => $name) {
            if (!is_string($name)) {
                continue;
            }

            $formattedName = self::formatFileName($name);
            // A ordem das chaves espelha a estrutura de $_FILES e faz parte do retorno público
            // (array_values, json_encode e comparação com === dependem dela), por isso não é alfabética.
            // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys
            $restructuredFiles[] = [
                'name' => $formattedName,
                'type' => self::fileEntryValue($file, 'type', $key, ''),
                'tmp_name' => self::fileEntryValue($file, 'tmp_name', $key, ''),
                'error' => self::fileEntryValue($file, 'error', $key, 0),
                'size' => self::fileEntryValue($file, 'size', $key, 0),
                'name_upload' => self::generateFileName($formattedName),
            ];
        }

        return $restructuredFiles;
    }
}
