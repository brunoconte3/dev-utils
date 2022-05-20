<?php

namespace DevUtils;

use DevUtils\{
    ValidateDate,
    ValidatePhone,
    ValidateFile,
};
use DevUtils\DependencyInjection\FormatAux;

class Format extends FormatAux
{
    /**
     * @param float|int|string $value
     */
    private static function formatCurrencyForFloat($value): float
    {
        if (is_string($value)) {
            if (preg_match('/(\,|\.)/', substr(substr($value, -3), 0, 1))) {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
                $value = substr_replace($value, '.', -2, 0);
            } else {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
            };
        }
        return (float) $value;
    }

    private static function formatFileName(string $fileName = ''): string
    {
        $dataName = explode('.', trim($fileName));
        $ext  = end($dataName);

        if (count($dataName) > 1) {
            unset($dataName[count($dataName) - 1]);
        }

        $dataName = implode('_', $dataName);
        $dataName = preg_replace('/\W/', '_', strtolower(self::removeSpecialCharacters($dataName)));

        return "{$dataName}.{$ext}";
    }

    private static function generateFileName(string $nameFile = ''): string
    {
        return date("d-m-Y_s_") . uniqid(rand() . rand() . rand() . time()) . '_' . $nameFile;
    }

    public static function convertTypes(array &$data, array $rules)
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
                } catch (\Exception $ex) {
                    $error[] = "falhar ao tentar converter {$data[$key]} para $type";
                }
            }
        }
        if (!empty($error)) {
            return $error;
        }
    }

    public static function companyIdentification(string $cnpj): string
    {
        parent::validateForFormatting('companyIdentification', 14, $cnpj);
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
    }

    public static function identifier(string $cpf): string
    {
        parent::validateForFormatting('identifier', 11, $cpf);
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
    }

    public static function identifierOrCompany(string $cpfCnpj): string
    {
        if (strlen($cpfCnpj) === 11) {
            return self::identifier($cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return self::companyIdentification($cpfCnpj);
        } else {
            throw new \Exception('identifierOrCompany => Valor precisa ser um CPF ou CNPJ!');
        }
    }

    /**
     * @param string|int $number Pode receber uma String ou Inteiro, compatibilidade com sistemas que já usam
     */
    public static function telephone($number): string
    {
        if (strlen($number) < 10 || strlen($number) > 11) {
            throw new \Exception('telephone precisa ter 10 ou 11 números!');
        }
        if (!is_numeric($number)) {
            throw new \Exception('telephone precisa conter apenas números!');
        }
        $number = '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
        return $number;
    }

    public static function zipCode(string $value): string
    {
        parent::validateForFormatting('zipCode', 8, $value);
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public static function dateBrazil(string $date)
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new \Exception('dateBrazil precisa conter 8 à 10 dígitos!');
        }
        return date('d/m/Y', strtotime($date));
    }

    public static function dateAmerican(string $date)
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new \Exception('dateAmerican precisa conter 8 à 10 dígitos!');
        }
        if (strpos($date, '/') > -1) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        return date('Y-m-d', strtotime($date));
    }

    public static function arrayToIntReference(array &$array): void
    {
        $array = array_map('intval', $array);
    }

    public static function arrayToInt(array $array): array
    {
        return array_map('intval', $array);
    }

    /**
     * @param float|int|string $value
     */
    public static function currency($value, string $coinType = ''): string
    {
        $value = self::formatCurrencyForFloat($value);
        return (!empty($value)) ? $coinType . number_format((float) $value, 2, ',', '.') : '';
    }

    /**
     * @param float|int|string $value
     */
    public static function currencyUsd($value, string $coinType = ''): string
    {
        $value = self::formatCurrencyForFloat($value);
        return (!empty($value)) ?  $coinType . number_format((float) $value, 2, '.', ',') : '';
    }

    /**
     * @return string|bool
     */
    public static function returnPhoneOrAreaCode(string $phone, bool $areaCode = false)
    {
        $phone = self::onlyNumbers($phone);
        if (!empty($phone) && ValidatePhone::validate($phone)) {
            return ($areaCode) ? preg_replace('/\A.{2}?\K[\d]+/', '', $phone) : preg_replace('/^\d{2}/', '', $phone);
        }
        return false;
    }

    public static function ucwordsCharset(string $string, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($string, $charset), MB_CASE_TITLE, 'UTF-8');
    }

    public static function pointOnlyValue(string $str): string
    {
        return preg_replace('/[^0-9]/', '.', preg_replace('/[^0-9,]/', '', $str));
    }

    public static function emptyToNull(array $array, string $exception = null): array
    {
        return array_map(function ($value) use ($exception) {
            if (isset($value) && is_array($value)) {
                return count($value) > 0 ? $value : null;
            }
            return (isset($value) && empty(trim($value))
                && $value !== $exception || $value === 'null') ? null : $value;
        }, $array);
    }

    public static function mask($mask, $str): string
    {
        $str = str_replace(' ', '', $str);
        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "#")] = $str[$i];
        }
        return $mask;
    }

    public static function onlyNumbers(string $str): string
    {
        return preg_replace('/[^0-9]/', '', $str);
    }

    public static function onlyLettersNumbers(string $str): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $str);
    }

    public static function upper(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtoupper($string, $charset);
    }

    public static function lower(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtolower($string, $charset);
    }

    public static function maskStringHidden(string $string, int $qtdHidden, int $positionHidden, string $char): ?string
    {
        if (empty(trim($string))) {
            return null;
        }
        if ($qtdHidden > strlen($string)) {
            throw new \Exception('Quantidade de caracteres para ocultar não pode ser maior que a String!');
        }
        if ($qtdHidden < 1) {
            throw new \Exception('Quantidade de caracteres para ocultar não pode ser menor que 1!');
        }
        $chars = str_repeat($char, $qtdHidden);
        return substr_replace($string, $chars, $positionHidden, strlen($chars));
    }

    public static function reverse(string $string, string $charSet = 'UTF-8'): string
    {
        if (!extension_loaded('iconv')) {
            throw new \Exception(__METHOD__ . '() requires ICONV extension that is not loaded.');
        }
        return iconv('UTF-32LE', $charSet, strrev(iconv($charSet, 'UTF-32BE', $string)));
    }

    public static function falseToNull($value)
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

    public static function removeSpecialCharacters(?string $string, bool $space = true): ?string
    {
        if (empty($string)) {
            return null;
        }
        $newString = self::removeAccent($string);
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

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        return strftime('%A, %d de %B de %Y', strtotime($date));
    }

    public static function writeCurrencyExtensive(float $numeral): string
    {
        if ($numeral <= 0) {
            throw new \Exception('O valor numeral deve ser maior que zero!');
        } else {
            return parent::extensive($numeral);
        }
    }

    public static function restructFileArray(array $file = []): array
    {
        $arrayFile = [];

        if (count($file) > 0) {
            $fileError = ValidateFile::validateFileErrorPhp($file);

            if (count($fileError) > 0) {
                return $fileError;
            }

            foreach ($file['name'] as $key => $name) {
                $name = self::formatFileName($name);
                $params = [
                    'name'     => $name,
                    'type'     => $file['type'][$key],
                    'tmp_name' => $file['tmp_name'][$key],
                    'error'    => $file['error'][$key],
                    'size'     => $file['size'][$key],
                    'name_upload' => self::generateFileName($name)
                ];
                array_push($arrayFile, $params);
            }
        }
        return $arrayFile;
    }

    public static function convertTimestampBrazilToAmerican(string $dt): string
    {
        if (!ValidateDate::validateTimeStamp($dt)) {
            throw new \Exception('Data não é um Timestamp!');
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $dt);
        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function convertStringToBinary(string $string): string
    {
        $characters = str_split($string);
        $binario = [];
        foreach ($characters as $character) {
            $data = unpack('H*', $character);
            $binario[] = base_convert($data[1], 16, 2);
        }
        return implode(' ', $binario);
    }

    public static function slugfy(string $text): string
    {
        return str_replace(' ', '-', self::lower(self::removeSpecialCharacters(str_replace('-', ' ', $text))));
    }
}
