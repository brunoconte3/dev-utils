<?php

declare(strict_types=1);

namespace DevUtils\DependencyInjection;

trait TraitRule
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private static function ruleDefinitionsAtoL(): array
    {
        return [
            'alpha' => ['validateAlphabets', Rules::CALL_WITHOUT_RULE],
            'alphaNoSpecial' => ['validateAlphaNoSpecial', Rules::CALL_WITHOUT_RULE],
            'alphaNum' => ['validateAlphaNumerics', Rules::CALL_WITHOUT_RULE],
            'alphaNumNoSpecial' => ['validateAlphaNumNoSpecial', Rules::CALL_WITHOUT_RULE],
            'array' => ['validateArray', Rules::CALL_WITHOUT_RULE],
            'arrayValues' => ['validateArrayValues', Rules::CALL_WITH_RULE],
            'bool' => ['validateBoolean', Rules::CALL_WITHOUT_RULE],
            'companyIdentification' => ['validateCompanyIdentification', Rules::CALL_WITH_RULE],
            'dateAmerican' => ['validateDateAmerican', Rules::CALL_WITHOUT_RULE],
            'dateBrazil' => ['validateDateBrazil', Rules::CALL_WITHOUT_RULE],
            'dateIso8601' => ['validateDateIso8601', Rules::CALL_WITHOUT_RULE],
            'dateNotFuture' => ['validateDateNotFuture', Rules::CALL_WITHOUT_RULE],
            'dateUTCWithoutTimezone' => ['validateDateUTCWithoutTimezone', Rules::CALL_WITHOUT_RULE],
            'email' => ['validateEmail', Rules::CALL_WITHOUT_RULE],
            'equals' => ['validateEquals', Rules::CALL_WITH_DATA],
            'fileName' => ['validateFileName', Rules::CALL_WITHOUT_RULE],
            'float' => ['validateFloating', Rules::CALL_WITHOUT_RULE],
            'hour' => ['validateHour', Rules::CALL_WITHOUT_RULE],
            'identifier' => ['validateIdentifier', Rules::CALL_WITHOUT_RULE],
            'identifierOrCompany' => ['validateIdentifierOrCompany', Rules::CALL_WITH_RULE],
            'int' => ['validateInteger', Rules::CALL_WITHOUT_RULE],
            'integer' => ['validateIntegerTyped', Rules::CALL_WITHOUT_RULE],
            'ip' => ['validateIp', Rules::CALL_WITHOUT_RULE],
            'json' => ['validateJson', Rules::CALL_WITHOUT_RULE],
            'lower' => ['validateLower', Rules::CALL_WITHOUT_RULE],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private static function ruleDefinitionsMtoN(): array
    {
        return [
            'mac' => ['validateMac', Rules::CALL_WITHOUT_RULE],
            'max' => ['validateMaximumField', Rules::CALL_WITH_RULE],
            'maxFile' => ['validateMaximumFileNumbers', Rules::CALL_WITH_RULE],
            'maxHeight' => ['validateMaxHeight', Rules::CALL_WITH_RULE],
            'maxUploadSize' => ['validateFileMaxUploadSize', Rules::CALL_WITH_RULE],
            'maxWidth' => ['validateMaxWidth', Rules::CALL_WITH_RULE],
            'maxWords' => ['validateMaximumWords', Rules::CALL_WITH_RULE],
            'mimeType' => ['validateFileMimeType', Rules::CALL_WITH_RULE],
            'min' => ['validateMinimumField', Rules::CALL_WITH_RULE],
            'minFile' => ['validateMinimumFileNumbers', Rules::CALL_WITH_RULE],
            'minHeight' => ['validateMinHeight', Rules::CALL_WITH_RULE],
            'minUploadSize' => ['validateFileMinUploadSize', Rules::CALL_WITH_RULE],
            'minWidth' => ['validateMinWidth', Rules::CALL_WITH_RULE],
            'minWords' => ['validateMinimumWords', Rules::CALL_WITH_RULE],
            'notSpace' => ['validateSpace', Rules::CALL_WITHOUT_RULE],
            'noWeekend' => ['validateWeekend', Rules::CALL_WITHOUT_RULE],
            'numeric' => ['validateNumeric', Rules::CALL_WITHOUT_RULE],
            'numericPassword' => ['validateNumericPassword', Rules::CALL_WITHOUT_RULE],
            'numMax' => ['validateNumMax', Rules::CALL_WITH_RULE],
            'numMin' => ['validateNumMin', Rules::CALL_WITH_RULE],
            'numMonth' => ['validateNumMonth', Rules::CALL_WITHOUT_RULE],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private static function ruleDefinitionsOtoZ(): array
    {
        return [
            'ddd' => ['validateDdd', Rules::CALL_WITH_RULE],
            'optional' => ['validateOptional', Rules::CALL_WITH_RULE],
            'password' => ['validatePassword', Rules::CALL_WITHOUT_RULE],
            'phone' => ['validatePhone', Rules::CALL_WITHOUT_RULE],
            'plate' => ['validatePlate', Rules::CALL_WITHOUT_RULE],
            'regex' => ['validateRegex', Rules::CALL_WITH_RULE],
            'required' => ['validateFieldMandatory', Rules::CALL_WITHOUT_RULE],
            'requiredFile' => ['validateFileUploadMandatory', Rules::CALL_WITHOUT_RULE],
            'requiredIf' => ['validateRequiredIf', Rules::CALL_WITH_DATA],
            'rgbColor' => ['validateRgbColor', Rules::CALL_WITHOUT_RULE],
            'timestamp' => ['validateTimestamp', Rules::CALL_WITHOUT_RULE],
            'type' => ['validateFieldType', Rules::CALL_WITH_RULE],
            'upper' => ['validateUpper', Rules::CALL_WITHOUT_RULE],
            'url' => ['validateUrl', Rules::CALL_WITHOUT_RULE],
            'zipcode' => ['validateZipCode', Rules::CALL_WITHOUT_RULE],
        ];
    }
}
