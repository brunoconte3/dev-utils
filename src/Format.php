<?php

declare(strict_types=1);

namespace DevUtils;

use DevUtils\DependencyInjection\FormatAux;
use DevUtils\DependencyInjection\TraitFormatCurrency;
use DevUtils\DependencyInjection\TraitFormatDate;
use DevUtils\DependencyInjection\TraitFormatFile;
use DevUtils\DependencyInjection\TraitFormatString;
use InvalidArgumentException;

class Format extends FormatAux
{
    use TraitFormatDate;
    use TraitFormatCurrency;
    use TraitFormatFile;
    use TraitFormatString;

    /**
     * @return array<int, string>
     */
    public static function convertTypes(array &$data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $key => $rule) {
            if (!is_string($rule)) {
                continue;
            }

            $ruleParts = explode('|', $rule);
            $type = parent::returnTypeToConvert($ruleParts);

            if ($type === null || !in_array('convert', $ruleParts, true) || !array_key_exists($key, $data)) {
                continue;
            }

            $converted = parent::executeConvert($type, $data[$key]);
            $succeeded = match ($type) {
                'bool' => is_bool($converted),
                'int' => is_int($converted),
                default => is_float($converted),
            };

            if (!$succeeded) {
                $errors[] = "Falha ao converter o campo '{$key}' para {$type}!";
                continue;
            }

            $data[$key] = $converted;
        }

        return $errors;
    }

    public static function companyIdentification(string $cnpj): string
    {
        $companyIdentification = (string) preg_replace('/[^A-Z0-9]/', '', strtoupper($cnpj));

        if (!preg_match('/^[A-Z0-9]{12}\d{2}$/', $companyIdentification)) {
            throw new InvalidArgumentException(
                'companyIdentification precisa conter 12 caracteres alfanuméricos seguidos de 2 dígitos!',
            );
        }

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($companyIdentification, 0, 2),
            substr($companyIdentification, 2, 3),
            substr($companyIdentification, 5, 3),
            substr($companyIdentification, 8, 4),
            substr($companyIdentification, 12, 2),
        );
    }

    public static function identifier(string $cpf): string
    {
        $sanitized = self::onlyLettersNumbers($cpf);
        parent::validateForFormatting('identifier', 11, $sanitized);
        $retorno = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $sanitized);
        return $retorno ?? '';
    }

    public static function identifierOrCompany(string $cpfCnpj): string
    {
        $sanitized = self::onlyLettersNumbers($cpfCnpj);

        if (strlen($sanitized) === 11) {
            return self::identifier($sanitized);
        }

        if (strlen($sanitized) === 14) {
            return self::companyIdentification($sanitized);
        }

        throw new InvalidArgumentException('identifierOrCompany => Valor precisa ser um CPF ou CNPJ!');
    }

    public static function telephone(string $number): string
    {
        if (!ctype_digit($number)) {
            throw new InvalidArgumentException('telephone precisa conter apenas números!');
        }
        if (strlen($number) < 10 || strlen($number) > 11) {
            throw new InvalidArgumentException('telephone precisa ter 10 ou 11 números!');
        }
        return '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
    }

    public static function zipCode(string $value): string
    {
        parent::validateForFormatting('zipCode', 8, $value);
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public static function arrayToIntReference(array &$array): void
    {
        $array = array_map(fn($v) => (int) $v, $array);
    }

    public static function arrayToInt(array $array): array
    {
        return array_map(fn($v) => (int) $v, $array);
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

    public static function emptyToNull(array $array, ?string $exception = null): array
    {
        return array_map(function ($value) use ($exception) {
            if (isset($value) && is_array($value)) {
                return count($value) > 0 ? $value : null;
            }
            return (isset($value) && empty(trim((string) $value)) && $value !== $exception)
                || $value === 'null' ? null : $value;
        }, $array);
    }

    public static function falseToNull(mixed $value): mixed
    {
        return $value === false ? null : $value;
    }
}
