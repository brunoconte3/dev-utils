<?php

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
            'validateFloating', 'validateJson',
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
            'maxUploadSize' => 'validateFileMaxUploadSize',
            'maxWidth' => 'validateMaxWidth',
            'maxHeight' => 'validateMaxHeight',
            'maxWords' => 'validateMaximumWords',
            'min' => 'validateMinimumField',
            'minFile' => 'validateMinimumFileNumbers',
            'minHeight' => 'validateMinHeight',
            'minWidth' => 'validateMinWidth',
            'mimeType' => 'validateFileMimeType',
            'minWords' => 'validateMinimumWords',
            'minUploadSize' => 'validateFileMinUploadSize',
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
