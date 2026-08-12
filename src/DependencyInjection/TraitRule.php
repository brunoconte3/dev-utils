<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitRule
{
    private function methodsNoRuleValue(): array
    {
        return [
            'validateAlphabets', 'validateAlphaNoSpecial', 'validateAlphaNumNoSpecial',
            'validateAlphaNumerics', 'validateEmail', 'validateIdentifier', 'validateIp',
            'validateLower', 'validateMac', 'validatePlate', 'validatePhone', 'validateRgbColor',
            'validateSpace', 'validateUpper', 'validateUrl', 'validateZipCode', 'validateInteger',
            'validateIntegerTyped', 'validateNumeric', 'validateNumMonth', 'validateFileName',
            'validateFileUploadMandatory', 'validateDateBrazil', 'validateDateAmerican', 'validateHour',
            'validateTimestamp', 'validateWeekend', 'validateArray', 'validateFieldMandatory', 'validateBoolean',
            'validateFloating', 'validateJson', 'validateDateNotFuture', 'validateDateIso8601',
            'validateDateUTCWithoutTimezone',
        ];
    }

    private static function functionsValidationAtoL(): array
    {
        return [
            'alpha' => 'validateAlphabets',
            'alphaNoSpecial' => 'validateAlphaNoSpecial',
            'alphaNum' => 'validateAlphaNumerics',
            'alphaNumNoSpecial' => 'validateAlphaNumNoSpecial',
            'array' => 'validateArray',
            'arrayValues' => 'validateArrayValues',
            'bool' => 'validateBoolean',
            'companyIdentification' => 'validateCompanyIdentification',
            'dateAmerican' => 'validateDateAmerican',
            'dateBrazil' => 'validateDateBrazil',
            'dateIso8601' => 'validateDateIso8601',
            'dateNotFuture' => 'validateDateNotFuture',
            'dateUTCWithoutTimezone' => 'validateDateUTCWithoutTimezone',
            'email' => 'validateEmail',
            'equals' => 'validateEquals',
            'fileName' => 'validateFileName',
            'float' => 'validateFloating',
            'hour' => 'validateHour',
            'identifier' => 'validateIdentifier',
            'identifierOrCompany' => 'validateIdentifierOrCompany',
            'int' => 'validateInteger',
            'integer' => 'validateIntegerTyped',
            'ip' => 'validateIp',
            'json' => 'validateJson',
            'lower' => 'validateLower',
        ];
    }

    private static function functionsValidationMtoN(): array
    {
        return [
            'mac' => 'validateMac',
            'max' => 'validateMaximumField',
            'maxFile' => 'validateMaximumFileNumbers',
            'maxHeight' => 'validateMaxHeight',
            'maxUploadSize' => 'validateFileMaxUploadSize',
            'maxWidth' => 'validateMaxWidth',
            'maxWords' => 'validateMaximumWords',
            'mimeType' => 'validateFileMimeType',
            'min' => 'validateMinimumField',
            'minFile' => 'validateMinimumFileNumbers',
            'minHeight' => 'validateMinHeight',
            'minUploadSize' => 'validateFileMinUploadSize',
            'minWidth' => 'validateMinWidth',
            'minWords' => 'validateMinimumWords',
            'notSpace' => 'validateSpace',
            'noWeekend' => 'validateWeekend',
            'numeric' => 'validateNumeric',
            'numMax' => 'validateNumMax',
            'numMin' => 'validateNumMin',
            'numMonth' => 'validateNumMonth',
        ];
    }

    private static function functionsValidationOtoZ(): array
    {
        return [
            'ddd' => 'validateDdd',
            'optional' => 'validateOptional',
            'phone' => 'validatePhone',
            'plate' => 'validatePlate',
            'regex' => 'validateRegex',
            'required' => 'validateFieldMandatory',
            'requiredFile' => 'validateFileUploadMandatory',
            'rgbColor' => 'validateRgbColor',
            'timestamp' => 'validateTimestamp',
            'type' => 'validateFieldType',
            'upper' => 'validateUpper',
            'url' => 'validateUrl',
            'zipcode' => 'validateZipCode',
        ];
    }
}
