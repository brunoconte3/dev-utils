<?php

declare(strict_types=1);

namespace DevUtils;

class ValidateFile
{
    private static function validateFileTransformSingleToMultiple(array &$file = []): void
    {
        if (isset($file['name']) && !is_array($file['name'])) {
            foreach ($file as $paramFile => $value) {
                $file[$paramFile] = [$value];
            }
        }
    }

    private static function validateFileCount(array $file = []): int
    {
        if (!empty($file) && isset($file['name'])) {
            if (is_array($file['name']) == 1) {
                return (count($file['name']) === 1) && empty($file['name'][0]) ?
                    count($file['name']) - 1 : count($file['name']);
            } else {
                return (is_string($file['name']) && !empty($file['name'])) ? 1 : 0;
            }
        }
        return 0;
    }

    private static function validateFileSize(
        array $file,
        ?int $rule,
        ?string $message,
        callable $condition,
        string $type
    ): array {
        $arrayFileError = [];

        if (self::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);
            if (!isset($file['size']) || !is_array($file['size'])) {
                return $arrayFileError;
            }

            foreach ($file['size'] as $key => $size) {
                if ($condition($size, $rule)) {
                    $fileName = (is_array($file['name']) && isset($file['name'][$key]))
                        ? (string) $file['name'][$key] : 'arquivo';
                    $defaultMsg = "O arquivo {$fileName} deve conter, no {$type} {$rule} bytes!";
                    $msg = (!empty($message)) ? $message : $defaultMsg;
                    array_push($arrayFileError, $msg);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateFileErrorPhp(array &$file, string $message = ''): array
    {
        self::validateFileTransformSingleToMultiple($file);

        $phpFileErrors = [
            UPLOAD_ERR_OK         => 'Arquivo enviado com sucesso!',
            UPLOAD_ERR_INI_SIZE   => 'O arquivo enviado excede o limite definido na diretiva UPLOAD_MAX_FILESIZE
                                      do php.ini!',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o limite definido em MAX_FILE_SIZE, no fomulário HTML!',
            UPLOAD_ERR_PARTIAL    => 'O upload do arquivo, foi realizado parcialmente!',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado!',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausênte!',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar arquivo no disco!',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão PHP interrompeu o upload do arquivo!',
        ];

        $arrayFileError = [];
        if (!isset($file['error']) || !is_array($file['error'])) {
            return $arrayFileError;
        }
        foreach ($file['error'] as $key => $codeError) {
            if (($codeError > 0) && is_int($codeError) && (array_key_exists($codeError, $phpFileErrors))) {
                $fileName = (is_array($file['name']) && isset($file['name'][$key])) ? $file['name'][$key] : '';
                $nameFile = empty($fileName) ? '' : '[' . (string) $fileName . '] - ';
                $message = (!empty($message)) ? $nameFile . $message : $nameFile . $phpFileErrors[$codeError];

                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxUploadSize(
        int $rule = 0,
        array $file = [],
        ?string $message = '',
    ): array {
        return self::validateFileSize($file, $rule, $message, function ($size, $rule) {
            return $size > $rule;
        }, 'máximo');
    }

    public static function validateMinUploadSize(
        ?int $rule = 0,
        array $file = [],
        ?string $message = '',
    ): array {
        return self::validateFileSize($file, $rule, $message, function ($size, $rule) {
            return $size < $rule;
        }, 'mínimo');
    }

    public static function validateMinWidth(
        string $field,
        ?int $rule,
        array $file,
        ?string $message = '',
    ): array {
        return self::validateImageDimension($field, $rule, $file, $message, 'width', 'min', 0);
    }

    public static function validateMinHeight(
        string $field,
        ?int $rule,
        array $file,
        ?string $message = '',
    ): array {
        return self::validateImageDimension($field, $rule, $file, $message, 'height', 'min', 1);
    }

    public static function validateMaxWidth(
        string $field,
        ?int $rule,
        array $file,
        ?string $message = '',
    ): array {
        return self::validateImageDimension($field, $rule, $file, $message, 'width', 'max', 0);
    }

    public static function validateMaxHeight(
        string $field,
        ?int $rule,
        array $file,
        ?string $message = '',
    ): array {
        return self::validateImageDimension($field, $rule, $file, $message, 'height', 'max', 1);
    }

    private static function validateImageDimension(
        string $field,
        ?int $rule,
        array $file,
        ?string $message,
        string $dimension,
        string $type,
        int $imageSizeIndex
    ): array {
        $arrayFileError = [];

        if (self::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);
            if (!isset($file['tmp_name']) || !is_array($file['tmp_name'])) {
                return $arrayFileError;
            }

            foreach ($file['tmp_name'] as $tmpName) {
                if (!is_string($tmpName)) {
                    continue;
                }
                $imageSize = getimagesize($tmpName) ?: [0, 0];
                $value = $imageSize[$imageSizeIndex];

                $isInvalid = match ($type) {
                    'min' => $value > 0 && $value < $rule,
                    'max' => $value > 0 && $value > $rule,
                    default => false,
                };

                if ($isInvalid) {
                    $dimensionLabel = $dimension === 'width' ? 'comprimento' : 'altura';
                    $typeLabel = $type === 'min' ? 'menor' : 'maior';
                    $defaultMsg = "O campo {$field} não pode ser {$typeLabel} que {$rule} pexels de {$dimensionLabel}!";
                    $msg = (!empty($message)) ? $message : $defaultMsg;
                    array_push($arrayFileError, $msg);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateFileName(array $file = []): array
    {
        $arrayFileError = [];

        if (self::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);
            if (!isset($file['name']) || !is_array($file['name'])) {
                return $arrayFileError;
            }

            foreach ($file['name'] as $key => $fileName) {
                if (!is_string($fileName)) {
                    continue;
                }
                $noSpecialCharacter = Format::removeSpecialCharacters($fileName) ?? '';
                $file['name'][$key] = explode('.', strtolower(trim(
                    str_replace(' ', '', $noSpecialCharacter)
                )));
            }
        }
        return $arrayFileError;
    }

    public static function validateMimeType(
        string | array $rule = '',
        array $file = [],
        ?string $message = '',
    ): array {
        $arrayFileError = [];

        if (self::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            $rule = (is_array($rule)) ? array_map(fn($v) => is_string($v) ? trim($v) : $v, $rule) : trim($rule);
            if (!isset($file['name']) || !is_array($file['name'])) {
                return $arrayFileError;
            }

            foreach ($file['name'] as $fileName) {
                if (!is_string($fileName)) {
                    continue;
                }
                $ext = explode('.', $fileName);

                $messageMimeType = 'O arquivo ' . $fileName . ', contém uma extensão inválida!';
                $messageMimeType = (!empty($message)) ? $message : $messageMimeType;

                if (is_string($rule) && (strtolower(end($ext)) != strtolower($rule))) {
                    array_push($arrayFileError, $messageMimeType);
                    continue;
                }

                if (is_array($rule) && (!in_array(end($ext), $rule))) {
                    array_push($arrayFileError, $messageMimeType);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateFileUploadMandatory(
        string $field = '',
        array $file = [],
        ?string $message = '',
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} é obrigatório!";

        if (
            !empty($file) &&
            (isset($file['error'])) &&
            is_array($file['error']) == 1 &&
            isset($file['error'][0]) &&
            ($file['error'][0] === UPLOAD_ERR_NO_FILE) ||
            (is_array($file['error']) == 0 && $file['error'] === UPLOAD_ERR_NO_FILE)
        ) {
            array_push($arrayFileError, $message);
        }
        return $arrayFileError;
    }

    public static function validateMaximumFileNumbers(
        int | string $rule = 0,
        int | string $field = '',
        array $file = [],
        ?string $message = '',
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no máximo {$rule} arquivo(s)!";

        if (self::validateFileCount($file) > $rule) {
            array_push($arrayFileError, $message);
        }
        return $arrayFileError;
    }

    public static function validateMinimumFileNumbers(
        int | string $rule = 0,
        int | string $field = '',
        array $file = [],
        ?string $message = '',
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no mínimo {$rule} arquivo(s)!";

        if (self::validateFileCount($file) < $rule) {
            array_push($arrayFileError, $message);
        }
        return $arrayFileError;
    }
}
