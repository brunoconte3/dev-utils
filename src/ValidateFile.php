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
        if ((count($file) > 0) && (isset($file['name']))) {
            switch (is_array($file['name'])) {
                case 1:
                    if ((count($file['name']) == 1) && empty($file['name'][0])) {
                        return (count($file['name']) - 1);
                    }
                    return count($file['name']);
                    break;

                case 0:
                    return (is_string($file['name']) && !empty($file['name'])) ? 1 : 0;
                    break;
            }
        }

        return 0;
    }

    public static function validateFileErrorPhp(array &$file = [], string $message = null): array
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

        foreach ($file['error'] as $key => $codeError) {
            if (($codeError > 0) && (array_key_exists($codeError, $phpFileErrors))) {
                $nameFile = empty($file['name'][$key]) ? '' : '[' . $file['name'][$key] . '] - ';
                $message = (!empty($message)) ? $nameFile . $message : $nameFile . $phpFileErrors[$codeError];

                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxUploadSize(
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['size'] as $key => $size) {
                if ($size > $rule) {
                    $msgMaxSize = 'O arquivo ' . $file['name'][$key] . ' deve conter, no máximo ' . $rule . ' bytes!';
                    $msgMaxSize = (!empty($message)) ? $message : $msgMaxSize;

                    array_push($arrayFileError, $msgMaxSize);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateMinWidth(
        string $field,
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['tmp_name'] as $key => $tmpName) {
                list($width) = getimagesize($tmpName);

                if ($width < $rule) {
                    $msgMinWidth = "O campo $field não pode ser menor que $rule pexels de comprimento!";
                    $msgMinWidth = (!empty($message)) ? $message : $msgMinWidth;
                    array_push($arrayFileError, $msgMinWidth);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateMinHeight(
        string $field,
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['tmp_name'] as $key => $tmpName) {
                list(, $height) = getimagesize($tmpName);

                if ($height < $rule) {
                    $msgMinHeight = "O campo $field não pode ser menor que $rule pexels de altura!";
                    $msgMinHeight = (!empty($message)) ? $message : $msgMinHeight;
                    array_push($arrayFileError, $msgMinHeight);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxWidth(
        string $field,
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['tmp_name'] as $key => $tmpName) {
                list($width) = getimagesize($tmpName);

                if ($width > $rule) {
                    $msgMaxWidth = "O campo $field não pode ser maior que $rule pexels de comprimento!";
                    $msgMaxWidth = (!empty($message)) ? $message : $msgMaxWidth;
                    array_push($arrayFileError, $msgMaxWidth);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxHeight(
        string $field,
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['tmp_name'] as $key => $tmpName) {
                list(, $height) = getimagesize($tmpName);

                if ($height > $rule) {
                    $msgMaxHeight = "O campo $field não pode ser maior que $rule pexels de altura!";
                    $msgMaxHeight = (!empty($message)) ? $message : $msgMaxHeight;
                    array_push($arrayFileError, $msgMaxHeight);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateMinUploadSize(
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['size'] as $key => $size) {
                if ($size < $rule) {
                    $msgMinSize = 'O arquivo ' . $file['name'][$key] . ' deve conter, no máximo ' . $rule . ' bytes!';
                    $msgMinSize = (!empty($message)) ? $message : $msgMinSize;

                    array_push($arrayFileError, $msgMinSize);
                }
            }
        }
        return $arrayFileError;
    }

    public static function validateFileName(array $file = [], string $message = null): array
    {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            foreach ($file['name'] as $key => $fileName) {
                $file['name'][$key] = explode('.', strtolower(trim(
                    str_replace(' ', '', Format::removeAccent($fileName))
                )));
            }
        }
        return $arrayFileError;
    }

    /**
     * @param string|array $rule
     */
    public static function validateMimeType(
        $rule = '',
        array $file = [],
        string $message = null
    ): array {
        $arrayFileError = [];

        if (validateFile::validateFileCount($file) > 0) {
            self::validateFileTransformSingleToMultiple($file);

            $rule = (is_array($rule)) ? array_map('trim', $rule) : trim($rule);

            foreach ($file['name'] as $fileName) {
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
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} é obrigatório!";

        if ((count($file) > 0) && (isset($file['error']))) {
            switch (is_array($file['error'])) {
                case 1:
                    if (isset($file['error'][0]) && ($file['error'][0] === UPLOAD_ERR_NO_FILE)) {
                        array_push($arrayFileError, $message);
                    }
                    break;
                case 0:
                    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                        array_push($arrayFileError, $message);
                    }
                    break;
            }
        }

        return $arrayFileError;
    }

    public static function validateMaximumFileNumbers(
        $rule = 0,
        $field = '',
        array $file = [],
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no máximo {$rule} arquivo(s)!";

        if (validateFile::validateFileCount($file) > $rule) {
            array_push($arrayFileError, $message);
        }

        return $arrayFileError;
    }

    public static function validateMinimumFileNumbers(
        $rule = 0,
        $field = '',
        array $file = [],
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no mínimo {$rule} arquivo(s)!";

        if (validateFile::validateFileCount($file) < $rule) {
            array_push($arrayFileError, $message);
        }
        return $arrayFileError;
    }
}
