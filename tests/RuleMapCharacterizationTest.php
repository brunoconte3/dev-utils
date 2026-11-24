<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class RuleMapCharacterizationTest extends TestCase
{
    private function expectedMap(): array
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
            'ddd' => 'validateDdd',
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
            'numericPassword' => 'validateNumericPassword',
            'numMax' => 'validateNumMax',
            'numMin' => 'validateNumMin',
            'numMonth' => 'validateNumMonth',
            'optional' => 'validateOptional',
            'password' => 'validatePassword',
            'phone' => 'validatePhone',
            'plate' => 'validatePlate',
            'regex' => 'validateRegex',
            'required' => 'validateFieldMandatory',
            'requiredFile' => 'validateFileUploadMandatory',
            'requiredIf' => 'validateRequiredIf',
            'rgbColor' => 'validateRgbColor',
            'timestamp' => 'validateTimestamp',
            'type' => 'validateFieldType',
            'upper' => 'validateUpper',
            'url' => 'validateUrl',
            'zipcode' => 'validateZipCode',
        ];
    }

    public function testFunctionsValidationMapsEveryRuleToItsMethod(): void
    {
        $map = Validator::functionsValidation();
        ksort($map, SORT_STRING | SORT_FLAG_CASE);

        self::assertSame($this->expectedMap(), $map);
    }

    public function testEveryMappedMethodExists(): void
    {
        foreach (Validator::functionsValidation() as $rule => $method) {
            self::assertTrue(method_exists(Validator::class, $method), "Regra $rule aponta para $method inexistente");
        }
    }
}
