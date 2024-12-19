<?php

namespace DevUtils;

use DevUtils\{
    ValidateDate,
    ValidatePhone,
    ValidateFile,
};
use DevUtils\DependencyInjection\{
    FormatAux,
    StrfTime,
};
use Exception;
use InvalidArgumentException;

class Format extends FormatAux
{
    private static function formatCurrencyForFloat(float | int | string $value): float
    {
        if (is_string($value)) {
            $separador = str_contains($value, ',') ? ',' : '.';
            $value = explode($separador, $value);
            if (isset($value[1]) && strlen(strval($value[1])) === 1) {
                $value[1] = strval($value[1]) . '0';
            }
            $value = implode($separador, $value);
            if (preg_match('/(\,|\.)/', substr(substr($value, -3), 0, 1))) {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
                $value = substr_replace($value, '.', -2, 0);
            } else {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
            }
        }
        return floatval($value);
    }

    private static function formatFileName(string $fileName = ''): string
    {
        $dataName = explode('.', trim($fileName));
        $ext  = end($dataName);

        if (count($dataName) > 1) {
            unset($dataName[count($dataName) - 1]);
        }

        $dataName = implode('_', $dataName);
        $stringNoSpecial = self::removeSpecialCharacters($dataName) ?? '';
        $dataName = preg_replace('/\W/', '_', strtolower($stringNoSpecial));

        return "{$dataName}.{$ext}";
    }

    private static function generateFileName(?string $nameFile): string
    {
        return date("d-m-Y_s_") . uniqid(rand() . rand() . rand() . time()) . '_' . $nameFile;
    }

    public static function convertTypes(array &$data, array $rules): void
    {
        $error = [];
        foreach ($rules as $key => $value) {
            $arrRules = explode('|', $value);
            $type = parent::returnTypeToConvert($arrRules);
            if (in_array('convert', $arrRules) && !empty($type)) {
                try {
                    if (in_array($key, array_keys($data))) {
                        $data[$key] = parent::executeConvert($type, $data[$key]);
                    }
                } catch (Exception) {
                    $error[] = "falhar ao tentar converter {$data[$key]} para $type";
                }
            }
        }
    }

    public static function companyIdentification(string $cnpj): string
    {
        parent::validateForFormatting('companyIdentification', 14, $cnpj);
        $retorno = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
        return $retorno ?? '';
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
        if (strpos($date, '/') > -1) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        return date('Y-m-d', (strtotime($date) ?: null));
    }

    public static function arrayToIntReference(array &$array): void
    {
        $array = array_map('intval', $array);
    }

    public static function arrayToInt(array $array): array
    {
        return array_map('intval', $array);
    }

    public static function currency(float | int | string $value, ?string $coinType = ''): string
    {
        $value = self::formatCurrencyForFloat($value);
        return (!empty($value) || $value === 0 || $value === '0')  ?
            $coinType . number_format(floatval($value), 2, ',', '.') : '';
    }

    public static function currencyUsd(float | int | string $value, ?string $coinType = ''): string
    {
        $value = self::formatCurrencyForFloat($value);
        return (!empty($value)) ?  $coinType . number_format(floatval($value), 2, '.', ',') : '';
    }

    public static function returnPhoneOrAreaCode(string $phone, bool $areaCode = false): string | bool
    {
        $phone = self::onlyNumbers($phone);
        if (!empty($phone) && ValidatePhone::validate($phone)) {
            $retorno = ($areaCode) ? preg_replace('/\A.{2}?\K[\d]+/', '', $phone)
                : preg_replace('/^\d{2}/', '', $phone);
            return $retorno ?? '';
        }
        return false;
    }

    public static function ucwordsCharset(string $string, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($string, $charset), MB_CASE_TITLE, 'UTF-8');
    }

    public static function pointOnlyValue(string $str): string
    {
        $str = preg_replace('/[^0-9,]/', '', $str) ?? '';
        $retorno = preg_replace('/[^0-9]/', '.', $str);
        return $retorno ?? '';
    }

    public static function emptyToNull(array $array, ?string $exception = null): array
    {
        return array_map(function ($value) use ($exception) {
            if (isset($value) && is_array($value)) {
                return count($value) > 0 ? $value : null;
            }
            return (isset($value) && empty(trim($value))
                && $value !== $exception || $value === 'null') ? null : $value;
        }, $array);
    }

    public static function mask(string $mask, string $str): string
    {
        $str = str_replace(' ', '', $str);
        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "#")] = $str[$i];
        }
        return gettype($mask) === 'string' ? strval($mask) : '';
    }

    public static function onlyNumbers(string $str): string
    {
        $retorno = preg_replace('/[^0-9]/', '', $str);
        return $retorno ?? '';
    }

    public static function onlyLettersNumbers(string $str): string
    {
        $retorno = preg_replace('/[^a-zA-Z0-9]/', '', $str);
        return $retorno ?? '';
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
        if (strpos($date, '/') > -1) {
            $date = implode('-', array_reverse(explode('/', $date)));
        }
        return StrfTime::strftime('%A, %d de %B de %Y', strtotime($date), 'pt_BR');
    }

    public static function writeCurrencyExtensive(float $numeral): string
    {
        if ($numeral <= 0) {
            throw new InvalidArgumentException('O valor numeral deve ser maior que zero!');
        } else {
            return parent::extensive($numeral);
        }
    }

    public static function restructFileArray(array $file = []): array
    {
        $arrayFile = [];

        if (!empty($file)) {
            $fileError = ValidateFile::validateFileErrorPhp($file);

            if (!empty($fileError)) {
                return $fileError;
            }
            if (isset($file['name'])) {
                foreach ($file['name'] as $key => $name) {
                    $name = self::formatFileName($name);
                    $params = [
                        'name'     => $name,
                        'type'     => $file['type'][$key],
                        'tmp_name' => $file['tmp_name'][$key],
                        'error'    => $file['error'][$key],
                        'size'     => $file['size'][$key],
                        'name_upload' => self::generateFileName($name),
                    ];
                    array_push($arrayFile, $params);
                }
            }
        }
        return $arrayFile;
    }

    public static function convertTimestampBrazilToAmerican(string $dt): string
    {
        if (!ValidateDate::validateTimeStamp($dt)) {
            throw new InvalidArgumentException('Data não é um Timestamp!');
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $dt);
        return !empty($dateTime) ? $dateTime->format('Y-m-d H:i:s') : '';
    }

    public static function convertStringToBinary(string $string): string
    {
        $characters = str_split($string);
        $binario = [];
        foreach ($characters as $character) {
            $data = unpack('H*', $character) ?: [];
            $binario[] = base_convert($data[1], 16, 2);
        }
        return implode(' ', $binario);
    }

    public static function slugfy(string $text): string
    {
        $noSpecialCharacter = self::removeSpecialCharacters(str_replace('-', ' ', $text)) ?? '';
        return str_replace(' ', '-', self::lower($noSpecialCharacter));
    }
}
