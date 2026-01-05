<?php

namespace DevUtils;

use DevUtils\DependencyInjection\FormatAux;
use DevUtils\DependencyInjection\StrfTime;
use Exception;
use InvalidArgumentException;

class Format extends FormatAux
{
    private static function normalizeDateToBrazilian(string $date): string
    {
        if (str_contains($date, '/')) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        return $date;
    }

    private static function formatCurrencyForFloat(float | int | string $value): float
    {
        if (is_string($value)) {
            $separator = str_contains($value, ',') ? ',' : '.';
            $valueParts = explode($separator, $value);

            if (isset($valueParts[1]) && strlen((string) $valueParts[1]) === 1) {
                $valueParts[1] = (string) $valueParts[1] . '0';
            }

            $value = implode($separator, $valueParts);
            $onlyNumbers = self::onlyNumbers($value);
            $numericValue = strlen($onlyNumbers) > 0 ? $onlyNumbers : '000';

            if (preg_match('/(\,|\.)/', substr(substr($value, -3), 0, 1))) {
                $value = substr_replace($numericValue, '.', -2, 0);
            } else {
                $value = $numericValue;
            }
        }
        return (float) $value;
    }

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

    private static function formatCurrency(
        float $value,
        string $decimalSeparator,
        string $thousandsSeparator,
        string $prefix = ''
    ): string {
        return (!empty($value) || $value === 0.0)
            ? $prefix . number_format($value, 2, $decimalSeparator, $thousandsSeparator)
            : '';
    }

    public static function convertTypes(array &$data, array $rules): void
    {
        $error = [];
        foreach ($rules as $key => $value) {
            if (!is_string($value)) {
                continue;
            }
            $arrRules = explode('|', $value);
            $type = parent::returnTypeToConvert($arrRules);
            if (in_array('convert', $arrRules) && !empty($type)) {
                try {
                    if (in_array($key, array_keys($data))) {
                        $data[$key] = parent::executeConvert($type, $data[$key]);
                    }
                } catch (Exception) {
                    $dataValue = isset($data[$key]) ? (string) $data[$key] : 'null';
                    $error[] = "falhar ao tentar converter {$dataValue} para $type";
                }
            }
        }
    }

    public static function companyIdentification(string $cnpj): string
    {
        $companyIdentification = strtoupper((string) preg_replace('/[^A-Z0-9]/', '', $cnpj));

        if (!preg_match('/^[A-Z0-9]{12}\d{2}$/', $companyIdentification)) {
            throw new InvalidArgumentException(
                'companyIdentification precisa conter 12 caracteres alfanuméricos seguidos de 2 dígitos!'
            );
        }

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($companyIdentification, 0, 2),
            substr($companyIdentification, 2, 3),
            substr($companyIdentification, 5, 3),
            substr($companyIdentification, 8, 4),
            substr($companyIdentification, 12, 2)
        );
    }

    public static function identifier(string $cpf): string
    {
        parent::validateForFormatting('identifier', 11, $cpf);
        $retorno = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
        return $retorno ?? '';
    }

    public static function identifierOrCompany(string $cpfCnpj): string
    {
        if (strlen($cpfCnpj) === 11) {
            return self::identifier($cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return self::companyIdentification($cpfCnpj);
        } else {
            throw new InvalidArgumentException('identifierOrCompany => Valor precisa ser um CPF ou CNPJ!');
        }
    }

    public static function telephone(string $number): string
    {
        if (strlen($number) < 10 || strlen($number) > 11) {
            throw new InvalidArgumentException('telephone precisa ter 10 ou 11 números!');
        }
        if (!is_numeric($number)) {
            throw new InvalidArgumentException('telephone precisa conter apenas números!');
        }
        return '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
    }

    public static function zipCode(string $value): string
    {
        parent::validateForFormatting('zipCode', 8, $value);
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public static function dateBrazil(string $date): string
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new InvalidArgumentException('dateBrazil precisa conter 8 à 10 dígitos!');
        }
        return date('d/m/Y', (strtotime($date) ?: null));
    }

    public static function dateAmerican(string $date): string
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new InvalidArgumentException('dateAmerican precisa conter 8 à 10 dígitos!');
        }

        if (str_contains($date, '/')) {
            return self::normalizeDateToBrazilian($date);
        }

        return date('Y-m-d', strtotime($date) ?: 0);
    }

    public static function arrayToIntReference(array &$array): void
    {
        $array = array_map(fn($v) => (int) $v, $array);
    }

    public static function arrayToInt(array $array): array
    {
        return array_map(fn($v) => (int) $v, $array);
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

    public static function returnPhoneOrAreaCode(string $phone, bool $areaCode = false): string | bool
    {
        $numericPhone = self::onlyNumbers($phone);

        if (empty($numericPhone) || !ValidatePhone::validate($numericPhone)) {
            return false;
        }

        return $areaCode
            ? preg_replace('/\A.{2}?\K[\d]+/', '', $numericPhone) ?? ''
            : preg_replace('/^\d{2}/', '', $numericPhone) ?? '';
    }

    public static function ucwordsCharset(string $string, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($string, $charset), MB_CASE_TITLE, 'UTF-8');
    }

    public static function pointOnlyValue(string $value): string
    {
        $withComma = preg_replace('/[^0-9,]/', '', $value) ?? '';
        return preg_replace('/[^0-9]/', '.', $withComma) ?? '';
    }

    public static function emptyToNull(array $array, ?string $exception = null): array
    {
        return array_map(function ($value) use ($exception) {
            if (isset($value) && is_array($value)) {
                return count($value) > 0 ? $value : null;
            }
            return ((isset($value) && empty(trim((string) $value)) && $value !== $exception)
                || $value === 'null') ? null : $value;
        }, $array);
    }

    public static function mask(string $mask, string $value): string
    {
        $cleanValue = str_replace(' ', '', $value);

        for ($i = 0; $i < strlen($cleanValue); $i++) {
            $position = strpos($mask, "#");
            if ($position !== false) {
                $mask[$position] = $cleanValue[$i];
            }
        }

        return $mask;
    }

    public static function onlyNumbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value) ?? '';
    }

    public static function onlyLettersNumbers(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $value) ?? '';
    }

    public static function upper(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtoupper($string, $charset);
    }

    public static function lower(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtolower($string, $charset);
    }

    public static function maskStringHidden(
        string $string,
        int $qtdHidden,
        int $positionHidden,
        string $char,
    ): ?string {
        if (empty(trim($string))) {
            return null;
        }
        if ($qtdHidden > strlen($string)) {
            throw new
                InvalidArgumentException('Quantidade de caracteres para ocultar não pode ser maior que a String!');
        }
        if ($qtdHidden < 1) {
            throw new InvalidArgumentException('Quantidade de caracteres para ocultar não pode ser menor que 1!');
        }
        $chars = str_repeat($char, $qtdHidden);
        return substr_replace($string, $chars, $positionHidden, strlen($chars));
    }

    public static function reverse(string $string, string $charSet = 'UTF-8'): string
    {
        if (!extension_loaded('iconv')) {
            throw new InvalidArgumentException(__METHOD__ . '() requires ICONV extension that is not loaded.');
        }
        return iconv('UTF-32LE', $charSet, strrev(iconv($charSet, 'UTF-32BE', $string) ?: '')) ?: '';
    }

    public static function falseToNull(mixed $value): mixed
    {
        return $value === false ? null : $value;
    }

    public static function removeAccent(?string $string): ?string
    {
        if (empty($string)) {
            return null;
        }
        return preg_replace(
            [
                '/(á|à|ã|â|ä)/',
                '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/',
                '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/',
                '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/',
                '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/',
                '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/',
                '/(Ñ)/',
                '/(ç)/',
                '/(Ç)/',
            ],
            explode(' ', 'a A e E i I o O u U n N c C'),
            $string
        );
    }

    public static function removeSpecialCharacters(string $string, bool $space = true): ?string
    {
        if (empty($string)) {
            return null;
        }
        $newString = self::removeAccent($string) ?? '';
        if ($space) {
            return preg_replace("/[^a-zA-Z0-9 ]/", "", $newString);
        }
        return preg_replace("/[^a-zA-Z0-9]/", "", $newString);
    }

    public static function writeDateExtensive(string $date): string
    {
        $normalizedDate = self::normalizeDateToBrazilian($date);
        $timestamp = strtotime($normalizedDate);

        if ($timestamp === false) {
            throw new InvalidArgumentException('Invalid date format for writeDateExtensive.');
        }

        return StrfTime::strftime('%A, %d de %B de %Y', $timestamp, 'pt_BR');
    }

    public static function writeCurrencyExtensive(float $numeral): string
    {
        if ($numeral <= 0) {
            throw new InvalidArgumentException('O valor numeral deve ser maior que zero!');
        }
        return parent::extensive($numeral);
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
            $restructuredFiles[] = [
                'name'        => $formattedName,
                'type'        => is_array($file['type']) && isset($file['type'][$key]) ? $file['type'][$key] : '',
                'tmp_name'    => is_array($file['tmp_name'])
                    && isset($file['tmp_name'][$key]) ? $file['tmp_name'][$key] : '',
                'error'       => is_array($file['error']) && isset($file['error'][$key]) ? $file['error'][$key] : 0,
                'size'        => is_array($file['size']) && isset($file['size'][$key]) ? $file['size'][$key] : 0,
                'name_upload' => self::generateFileName($formattedName),
            ];
        }

        return $restructuredFiles;
    }

    public static function convertTimestampBrazilToAmerican(string $dt): string
    {
        if (!ValidateDate::validateTimeStamp($dt)) {
            throw new InvalidArgumentException('Data não é um Timestamp!');
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $dt);
        return $dateTime !== false ? $dateTime->format('Y-m-d H:i:s') : '';
    }

    public static function convertStringToBinary(string $string): string
    {
        $characters = str_split($string);
        $binaryParts = [];

        foreach ($characters as $character) {
            $hexData = unpack('H*', $character) ?: [];
            if (isset($hexData[1]) && is_string($hexData[1])) {
                $binaryParts[] = base_convert($hexData[1], 16, 2);
            }
        }

        return implode(' ', $binaryParts);
    }

    public static function slugfy(string $text): string
    {
        $noSpecialCharacter = self::removeSpecialCharacters(str_replace('-', ' ', $text)) ?? '';
        return str_replace(' ', '-', self::lower($noSpecialCharacter));
    }
}
