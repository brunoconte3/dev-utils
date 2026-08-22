<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    private const MIME_JPEG = 'image/jpeg';
    private const MIME_PDF = 'application/pdf';
    private const FILE_NAME_JPG = 'JPG - Validação upload v.1.jpg';
    private const FILE_NAME_PDF = 'PDF - Validação upload v.1.pdf';
    private const TMP_PATH_JPG = '/tmp/phpODnLGo';
    private const TMP_PATH_PDF = '/tmp/phpfmb0tL';
    private const RULE_ARRAY_VALUES = 'arrayValues:S-N-T';
    private const RULE_MAX_WORDS_2 = 'maxWords:2';
    private const RULE_MIN_WORDS_2 = 'minWords:2';
    private const RULE_MIN_5 = 'min:5';
    private const RULE_NUM_MIN_5 = 'numMin:5';
    private const RULE_REQUIRED_MIN_5 = 'required|min:5, ';
    private const RULE_OPTIONAL_MIN_5 = 'optional|min:5';
    private const RULE_OPTIONAL_MIN_3 = 'optional|min:3';
    private const RULE_MIN_3_MAX_10 = 'min:3|max:10';
    private const RULE_NUM_MIN_10_MAX_100 = 'numMin:10|numMax:100';
    private const VALUE_FULL_NAME = 'Bruno Conte';
    private const VALUE_LONG_TEXT = 'Ele usa um dicionário com mais de 200 palavras!';
    private const FORMAT_DATE_BRAZIL = 'd/m/Y';

    private function validate(array $data, array $rules): Validator
    {
        $validator = new Validator();
        $validator->set($data, $rules);
        return $validator;
    }

    private function assertErrorCount(int $expected, array $data, array $rules): void
    {
        $validator = $this->validate($data, $rules);
        self::assertCount($expected, $validator->getErros());
    }

    private function mountFileSingle(): array
    {
        $l = DIRECTORY_SEPARATOR;
        $archive = substr((string) realpath(dirname(__FILE__)), 0, -5) . 'public_html' . $l . 'static' . $l . 'img'
            . $l . 'iconTest.png';
        $archive = str_replace("\\", "\\/", $archive);
        return [
            'error' => 0,
            'name' => 'fileUpload ',
            'size' => 19639,
            'tmp_name' => $archive,
            'type' => self::MIME_JPEG,
        ];
    }

    private function mountFileDataRequired(): array
    {
        return [
            'error' => 4,
            'name' => '',
            'size' => 0,
            'tmp_name' => '',
            'type' => '',
        ];
    }

    private function mountMineTypeFile(int $size = 8488): array
    {
        return [
            'error' => 0,
            'name' => self::FILE_NAME_JPG,
            'size' => $size,
            'tmp_name' => self::TMP_PATH_JPG,
            'type' => self::MIME_JPEG,
        ];
    }

    public function testArray(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a', 'testValid' => ['a' => 1, 'b' => 2]],
            ['testError' => 'array', 'testValid' => 'array'],
        );
    }

    public function testArrayValues(): void
    {
        $array = ['testError' => 'M', 'testValid' => 'S',];
        $rules = ['testError' => self::RULE_ARRAY_VALUES, 'testValid' => self::RULE_ARRAY_VALUES,];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        $array = [
            'emptyArrayError' => [],
            'arrayDataError' => 11,
            'arrayDataRequired' => ['company' => 'cooper'],
        ];
        $rules = [
            'emptyArrayError' => 'required',
            'arrayDataError' => 'array',
            'arrayDataRequired' => 'required|array',
        ];
        $expected = [
            'emptyArrayError' => 'O campo emptyArrayError é obrigatório!',
            'arrayDataError' => 'A variável arrayDataError não é um array!',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
        self::assertSame($validator->getErros(), $expected, 'Erro');
    }

    public function testBool(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a123', 'testValid' => true],
            ['testError' => 'int', 'testValid' => 'bool'],
        );
    }

    public function testCompanyIdentification(): void
    {
        $array = [
            'testError' => '52186923000120',
            'testErrorEmpty' => '',
            'testExceptionError' => '12123456000712',
            'testExceptionValid' => '00000000000000',
            'testOtherValid' => 'JA.JL4.X24/9VI6-23',
            'testValid' => '21111527000163',
        ];
        $rules = [
            'testError' => 'companyIdentification',
            'testErrorEmpty' => 'companyIdentification',
            'testExceptionError' => 'companyIdentification:12123456000712',
            'testExceptionValid' => 'companyIdentification:00000000000000;22222222222222',
            'testOtherValid' => 'companyIdentification',
            'testValid' => 'companyIdentification',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testDateAmerican(): void
    {
        self::assertErrorCount(
            2,
            ['testError' => '1990-04-31', 'testErrorEmpty' => '', 'testValid' => '1990-04-30'],
            ['testError' => 'dateAmerican', 'testErrorEmpty' => 'dateAmerican', 'testValid' => 'dateAmerican'],
        );
    }

    public function testDateBrazil(): void
    {
        self::assertErrorCount(
            2,
            ['testError' => '31042020', 'testErrorEmpty' => '', 'testValid' => '31052020'],
            ['testError' => 'dateBrazil', 'testErrorEmpty' => 'dateBrazil', 'testValid' => 'dateBrazil'],
        );
    }

    public function testEmail(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'bruno.com', 'testValid' => 'brunoconte3@gmail.com'],
            ['testError' => 'email', 'testValid' => 'email'],
        );
    }

    public function testIdentifier(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '06669987788', 'testValid' => '55634405831'],
            ['testError' => 'identifier', 'testValid' => 'identifier'],
        );
    }

    public function testInt(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a123', 'testValid' => 123],
            ['testError' => 'int', 'testValid' => 'int'],
        );
    }

    public function testIp(): void
    {
        // @codingStandardsIgnoreStart
        $array = ['testError' => '1.1.0', 'testValid' => '10.202.0.58',]; // NOSONAR - Test not actual IP addresses
        // @codingStandardsIgnoreEnd
        self::assertErrorCount(1, $array, ['testError' => 'ip', 'testValid' => 'ip']);
    }

    public function testFloat(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a1', 'testValid' => '10.125'],
            ['testError' => 'float', 'testValid' => 'float'],
        );
    }

    public function testHour(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '24:03', 'testValid' => '21:03'],
            ['testError' => '{"type":"hour"}', 'testValid' => '{"type":"hour"}'],
        );
    }

    public function testLower(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Abcdção', 'testValid' => 'abcdção'],
            ['testError' => '{"type":"lower"}', 'testValid' => '{"type":"lower"}'],
        );
    }

    public function testMac(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '00:00', 'testValid' => '00-D0-56-F2-B5-12'],
            ['testError' => 'mac', 'testValid' => 'mac'],
        );
    }

    public function testMax(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 123, 'testValid' => "Avenida Pedra D'Água"],
            ['testError' => 'max:2', 'testValid' => 'max:20'],
        );
    }

    public function testMaxWords(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Jorge da Silva', 'testValid' => self::VALUE_FULL_NAME],
            ['testError' => self::RULE_MAX_WORDS_2, 'testValid' => self::RULE_MAX_WORDS_2],
        );
    }

    public function testMin(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '123', 'testValid' => "Avenida Pedra D'Água"],
            ['testError' => self::RULE_MIN_5, 'testValid' => 'min:20'],
        );
    }

    public function testMinWords(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Jorge da Silva', 'testValid' => self::VALUE_FULL_NAME],
            ['testError' => 'minWords:4', 'testValid' => self::RULE_MIN_WORDS_2],
        );
    }

    public function testNoWeekend(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '10/10/2020', 'testValid' => '16/10/2020'],
            ['testError' => 'noWeekend', 'testValid' => 'noWeekend'],
        );
    }

    public function testNumeric(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a', 'testValid' => 123],
            ['testError' => 'numeric', 'testValid' => 'numeric'],
        );
    }

    public function testNumMax(): void
    {
        $array = [
            'testError' => 32,
            'testErrorMaxZero' => '2',
            'testErrorNegative' => -1,
            'testValid' => 31,
        ];
        $rules = [
            'testError' => 'numMax:31',
            'testErrorMaxZero' => 'numMax:0',
            'testErrorNegative' => 'numMax:3',
            'testValid' => 'numMax:31',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testNumMin(): void
    {
        $array = [
            'testErrorNegative' => '-2',
            'testErrorNoInt' => 'a',
            'testError' => 2,
            'testValid' => 8,
            'testValidZero' => '0',
        ];
        $rules = [
            'testErrorNegative' => 'numMin:-2',
            'testErrorNoInt' => self::RULE_NUM_MIN_5,
            'testError' => self::RULE_NUM_MIN_5,
            'testValid' => self::RULE_NUM_MIN_5,
            'testValidZero' => 'numMin:0',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testOptional(): void
    {
        $validator = new Validator();
        $validator->set(['test' => null,], ['test' => 'optional|min:2|int',]);
        self::assertFalse(!empty($validator->getErros()));
    }

    public function testParamJson(): void
    {
        $array = [
            'testError' => '@&451',
            'testValid' => 123,
        ];
        $rules = [
            'testError' => '{"required":"true","type":"alpha"}',
            'testValid' => '{"required":"true","type":"int"}',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testPhone(): void
    {
        $array = [
            'testError' => '444569874',
            'testInvalidRule' => 'br',
            'testMask' => '(44) 99932-5847',
            'testValid' => '4433467847',
        ];
        $rules = [
            'testError' => 'phone',
            'testInvalidRule' => 'thisRuleDoesNotExist',
            'testMask' => 'phone',
            'testValid' => 'phone',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testPlate(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'AXI-36688', 'testValid' => 'AXI-3668', 'testValidNoMask' => 'aXI3668'],
            ['testError' => 'plate', 'testValid' => 'plate', 'testValidNoMask' => 'plate'],
        );
    }

    public function testRegex(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'bruno_conte3', 'testValid' => self::VALUE_FULL_NAME],
            ['testError' => 'regex:/^[a-zA-Z\s]+$/', 'testValid' => 'regex:/^[a-zA-Z\s]+$/'],
        );
    }

    public function testRequired(): void
    {
        $array = [
            '',
            null,
            false,
            [],
            '   ',
            'abc',
            123,
            '0',
            0,
            '<p>Texto com HTML <span style="color: #3598db;">sadasdasdasd</span></p>',
        ];
        $rules = [];
        foreach (array_keys($array) as $key) {
            $rules[$key] = 'required';
        }

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(5, $validator->getErros());
    }

    public function testType(): void
    {
        $array = [
            'testAlphaError' => self::VALUE_LONG_TEXT,
            'testAlphaNoSpecialError' => self::VALUE_LONG_TEXT,
            'testAlphaNoSpecialValid' => 'Ele usa um dicionario com mais de X palavras',
            'testAlphaNumError' => self::VALUE_LONG_TEXT,
            'testAlphaNumNoSpecialError' => self::VALUE_LONG_TEXT,
            'testAlphaNumNoSpecialValid' => 'Ele usa um dicionario com mais de 200 palavras',
            'testAlphaNumValid' => 'Ele usa um dicionário com mais de 200 palavras',
            'testAlphaValid' => 'Ele usa um dicionário com mais de X palavras',
        ];
        $rules = [
            'testAlphaError' => 'type:alpha',
            'testAlphaNoSpecialError' => 'type:alphaNoSpecial',
            'testAlphaNoSpecialValid' => 'type:alphaNoSpecial',
            'testAlphaNumError' => 'type:alphaNum',
            'testAlphaNumNoSpecialError' => 'type:alphaNumNoSpecial',
            'testAlphaNumNoSpecialValid' => 'type:alphaNumNoSpecial',
            'testAlphaNumValid' => 'type:alphaNum',
            'testAlphaValid' => 'type:alpha',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(4, $validator->getErros());
    }

    public function testUpper(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'AbcDçÃo', 'testValid' => 'ABCDÇÃO'],
            ['testError' => 'upper', 'testValid' => 'upper'],
        );
    }

    public function testUrl(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'ww.test.c', 'testValid' => 'https://www.google.com.br'],
            ['testError' => 'url', 'testValid' => 'url'],
        );
    }

    public function testZipCode(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '870475', 'testValid' => '87047510'],
            ['testError' => 'zipcode', 'testValid' => 'zipcode'],
        );
    }

    public function testCustomMessage(): void
    {
        $msg = 'Mensagem customizada aqui, devendo conter no mínimo uma vírgula!';
        $array = [
            'textError' => 'abc',
            'textValid' => 'abcde',
        ];
        $rules = [
            'textError' => self::RULE_REQUIRED_MIN_5 . $msg . '|max:20',
            'textValid' => self::RULE_REQUIRED_MIN_5 . $msg . '|max:20',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertEquals($msg, $validator->getErros()['textError']);
    }

    public function testNotSpace(): void
    {
        self::assertErrorCount(
            1,
            ['spaceError' => 'BRU C', 'spaceValid' => 'BRUC'],
            ['spaceError' => 'notSpace', 'spaceValid' => 'notSpace'],
        );
    }

    public function testJson(): void
    {
        self::assertErrorCount(
            1,
            ['jsonError' => '"name": "Bruno"}', 'jsonValid' => '{"name": "Bruno"}'],
            ['jsonError' => 'type:json', 'jsonValid' => 'type:json'],
        );
    }

    public function testNumMonth(): void
    {
        self::assertErrorCount(
            1,
            ['monthError' => 13, 'monthValid' => 10],
            ['monthError' => 'numMonth', 'monthValid' => 'numMonth'],
        );
    }

    public function testIdentifierOrCompany(): void
    {
        $array = [
            'cpfOrCnpjError' => '9E.2A4.092.0001/5A',
            'cpfOrCnpjExceptionError' => '12.123.456/0007-12',
            'cpfOrCnpjExceptionValid' => '00.000.000/0000-00',
            'cpfOrCnpjInvalid' => '0966894790',
            'cpfOrCnpjValid' => 'DE.VUT.ILS/123X-49',
        ];
        $rules = [
            'cpfOrCnpjError' => 'identifierOrCompany',
            'cpfOrCnpjExceptionError' => 'identifierOrCompany:12123456000712',
            'cpfOrCnpjExceptionValid' => 'identifierOrCompany:00000000000000;22222222222222',
            'cpfOrCnpjInvalid' => 'identifierOrCompany',
            'cpfOrCnpjValid' => 'identifierOrCompany',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testFileMaxUploadSize(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile();
        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => self::FILE_NAME_PDF,],
            'size' => ['0' => 8488, '1' => 818465,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => self::TMP_PATH_PDF,],
            'type' => ['0' => self::MIME_JPEG, '1' => self::MIME_PDF,],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'maxUploadSize:5550',
            'fileUploadSingle' => 'maxUploadSize:5550',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileMinUploadSize(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile(3589);
        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => self::FILE_NAME_PDF,],
            'size' => ['0' => 4450, '1' => 4980,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => self::TMP_PATH_PDF,],
            'type' => ['0' => self::MIME_JPEG, '1' => self::MIME_PDF,],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'minUploadSize:5550',
            'fileUploadSingle' => 'minUploadSize:5550',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileMimeType(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile();
        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => self::FILE_NAME_PDF,],
            'size' => ['0' => 8488, '1' => 818465,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => self::TMP_PATH_PDF,],
            'type' => ['0' => self::MIME_JPEG, '1' => self::MIME_PDF,],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'mimeType:png;svg',
            'fileUploadSingle' => 'mimeType:jpeg;png',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileName(): void
    {
        $fileUploadSingle = [
            'error' => 0,
            'name' => self::FILE_NAME_JPG,
            'size' => 8488,
            'tmp_name' => self::TMP_PATH_JPG,
            'type' => self::MIME_JPEG,
        ];
        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => self::FILE_NAME_PDF,],
            'size' => ['0' => 8488, '1' => 818465,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => self::TMP_PATH_PDF,],
            'type' => ['0' => self::MIME_JPEG, '1' => self::MIME_PDF,],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'fileName',
            'fileUploadSingle' => 'fileName',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertFalse(!empty($validator->getErros()));
    }

    public function testRequiredFile(): void
    {
        $fileUploadSingle = $this->mountFileDataRequired();
        $fileUploadMultiple = [
            'error' => ['0' => 4,],
            'name' => ['0' => '',],
            'size' => ['0' => 0,],
            'tmp_name' => ['0' => '',],
            'type' => ['0' => '',],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'requiredFile',
            'fileUploadSingle' => 'requiredFile',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMaxFile(): void
    {
        $fileUploadMultiple = [
            'error' => ['0' => 0, '1' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG, '1' => self::FILE_NAME_PDF,],
            'size' => ['0' => 8488, '1' => 818465,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG, '1' => self::TMP_PATH_PDF,],
            'type' => ['0' => self::MIME_JPEG, '1' => self::MIME_PDF,],
        ];
        $array = ['fileUploadMultiple' => $fileUploadMultiple];
        self::assertErrorCount(1, $array, ['fileUploadMultiple' => 'maxFile:1']);
    }

    public function testMinFile(): void
    {
        $fileUploadSingle = $this->mountFileDataRequired();
        $fileUploadMultiple = [
            'error' => ['0' => 0,],
            'name' => ['0' => self::FILE_NAME_JPG,],
            'size' => ['0' => 8488,],
            'tmp_name' => ['0' => self::TMP_PATH_JPG,],
            'type' => ['0' => self::MIME_JPEG,],
        ];
        $array = [
            'fileUploadMultiple' => $fileUploadMultiple,
            'fileUploadSingle' => $fileUploadSingle,
        ];
        $rules = [
            'fileUploadMultiple' => 'minFile:2',
            'fileUploadSingle' => 'minFile:1',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testTimestamp(): void
    {
        $array = [
            'dateHourAmericanError' => '2021-04-15 21:01',
            'dateHourAmericanValid' => '2021-04-15 21:01:04',
            'dateHourBrazilError' => '15/04/2021 21:01',
            'dateHourBrazilValid' => '15/04/2021 21:01:04',
        ];
        $rules = [
            'dateHourAmericanError' => 'timestamp',
            'dateHourAmericanValid' => 'timestamp',
            'dateHourBrazilError' => 'timestamp',
            'dateHourBrazilValid' => 'timestamp',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testEquals(): void
    {
        $array = [
            'confirmPassword' => 'reset123',
            'invalidRule' => 'test',
            'password' => 'reset',
        ];
        $rules = [
            'confirmPassword' => 'min:3|max:5|alpha|equals:password',
            'invalidRule' => 'equals',
            'password' => 'min:3|max:5|alpha',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMaxWidth(): void
    {
        $_FILES = $this->mountFileSingle();
        $array = [
            'fileUploadError' => $_FILES,
            'fileUploadValid' => $_FILES,
        ];
        $rules = [
            'fileUploadError' => 'maxWidth:100',
            'fileUploadValid' => 'maxWidth:200',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        if (extension_loaded('gd')) {
            self::assertCount(1, $validator->getErros());
        } else {
            self::assertFalse(extension_loaded('gd'));
        }
    }

    public function testMaxHeight(): void
    {
        $_FILES = $this->mountFileSingle();
        $array = [
            'fileUploadError' => $_FILES,
            'fileUploadValid' => $_FILES,
        ];
        $rules = [
            'fileUploadError' => 'maxHeight:100',
            'fileUploadValid' => 'maxHeight:200',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);

        if (extension_loaded('gd')) {
            self::assertCount(1, $validator->getErros());
        } else {
            self::assertFalse(extension_loaded('gd'));
        }
    }

    public function testMinWidth(): void
    {
        $_FILES = $this->mountFileSingle();
        $array = [
            'fileUploadError' => $_FILES,
            'fileUploadValid' => $_FILES,
        ];
        $rules = [
            'fileUploadError' => 'minWidth:500',
            'fileUploadValid' => 'minWidth:200',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        if (extension_loaded('gd')) {
            self::assertCount(1, $validator->getErros());
        } else {
            self::assertFalse(extension_loaded('gd'));
        }
    }

    public function testMinHeight(): void
    {
        $_FILES = $this->mountFileSingle();
        $array = [
            'fileUploadError' => $_FILES,
            'fileUploadValid' => $_FILES,
        ];
        $rules = [
            'fileUploadError' => 'minHeight:500',
            'fileUploadValid' => 'minHeight:200',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        if (extension_loaded('gd')) {
            self::assertCount(1, $validator->getErros());
        } else {
            self::assertFalse(extension_loaded('gd'));
        }
    }

    public function testDateUTCWithoutTimezone(): void
    {
        $array = [
            'dateError' => '2024-13-01T12:00:00',
            'dateValid' => '2024-12-01T12:00:00',
        ];
        $rules = [
            'dateError' => 'dateUTCWithoutTimezone',
            'dateValid' => 'dateUTCWithoutTimezone',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateIso8601(): void
    {
        $array = [
            'dateError' => '2024-13-01T12:00:00+00:00',
            'dateValid' => '2024-12-01T12:00:00+00:00',
        ];
        $rules = [
            'dateError' => 'dateIso8601',
            'dateValid' => 'dateIso8601',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateNotFuture(): void
    {
        $futureDate = date(self::FORMAT_DATE_BRAZIL, strtotime('+1 year'));
        $pastDate = date(self::FORMAT_DATE_BRAZIL, strtotime('-1 year'));
        $today = date(self::FORMAT_DATE_BRAZIL);
        $array = [
            'futureError' => $futureDate,
            'pastValid' => $pastDate,
            'todayValid' => $today,
        ];
        $rules = [
            'futureError' => 'dateNotFuture',
            'pastValid' => 'dateNotFuture',
            'todayValid' => 'dateNotFuture',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateNotFutureAmerican(): void
    {
        $futureDate = date('Y-m-d', strtotime('+1 month'));
        $pastDate = date('Y-m-d', strtotime('-1 month'));
        $array = [
            'futureError' => $futureDate,
            'pastValid' => $pastDate,
        ];
        $rules = [
            'futureError' => 'dateNotFuture',
            'pastValid' => 'dateNotFuture',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDdd(): void
    {
        $array = [
            'dddError' => '00',
            'dddValid' => '44',
            'dddValidThreeDigits' => '044',
        ];
        $rules = [
            'dddError' => 'ddd',
            'dddValid' => 'ddd',
            'dddValidThreeDigits' => 'ddd',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDddByState(): void
    {
        $array = [
            'dddPrError' => '11',
            'dddPrValid' => '44',
            'dddSpValid' => '11',
        ];
        $rules = [
            'dddPrError' => 'ddd:pr',
            'dddPrValid' => 'ddd:pr',
            'dddSpValid' => 'ddd:sp',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testRgbColor(): void
    {
        $array = [
            'rgbError' => '300, 100, 50',
            'rgbInvalidNegative' => '-1, 100, 50',
            'rgbValid' => '255, 100, 50',
            'rgbValidMax' => '255,255,255',
            'rgbValidNoSpaces' => '0,0,0',
        ];
        $rules = [
            'rgbError' => 'rgbColor',
            'rgbInvalidNegative' => 'rgbColor',
            'rgbValid' => 'rgbColor',
            'rgbValidMax' => 'rgbColor',
            'rgbValidNoSpaces' => 'rgbColor',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMultipleRulesOnSameField(): void
    {
        $array = [
            'email' => 'test@',
            'password' => 'ab',
        ];
        $rules = [
            'email' => 'required|email|max:50',
            'password' => 'required|min:6|max:20',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testValidatorWithEmptyData(): void
    {
        $validator = new Validator();
        $validator->set([], ['field' => 'required']);
        self::assertArrayHasKey('erro', $validator->getErros());
    }

    public function testValidatorWithNestedArray(): void
    {
        $array = [
            ['name' => 'Bruno', 'age' => 30],
            ['name' => '', 'age' => 25],
        ];
        $rules = [
            'age' => 'required|int',
            'name' => 'required|min:2',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testOptionalWithValue(): void
    {
        $array = ['field' => 'ab'];
        $rules = ['field' => self::RULE_OPTIONAL_MIN_5];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testOptionalWithEmptyValue(): void
    {
        $array = ['field' => ''];
        $rules = ['field' => self::RULE_OPTIONAL_MIN_5];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(0, $validator->getErros());
    }

    public function testEmailWithMax(): void
    {
        $longEmail = str_repeat('a', 50) . '@teste.com';
        $array = ['email' => $longEmail];
        $rules = ['email' => 'email|max:50'];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testPhoneWithMask(): void
    {
        $array = [
            'phone1' => '(44) 99999-8888',
            'phone2' => '(11) 3333-4444',
        ];
        $rules = [
            'phone1' => 'phone',
            'phone2' => 'phone',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testIdentifierWithMask(): void
    {
        $array = [
            'cpfInvalid' => '111.111.111-11',
            'cpfValid' => '556.344.058-31',
        ];
        $rules = [
            'cpfInvalid' => 'identifier',
            'cpfValid' => 'identifier',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testCompanyIdentificationWithMask(): void
    {
        $array = [
            'cnpjInvalid' => '11.111.111/1111-11',
            'cnpjValid' => '21.111.527/0001-63',
        ];
        $rules = [
            'cnpjInvalid' => 'companyIdentification',
            'cnpjValid' => 'companyIdentification',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testIntegerTyped(): void
    {
        $array = [
            'testError' => '123',
            'testValid' => 123,
        ];
        $rules = [
            'testError' => 'integer',
            'testValid' => 'integer',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testIntegerTypedWithZero(): void
    {
        $array = [
            'testValid' => 0,
            'testValidNegative' => -5,
        ];
        $rules = [
            'testValid' => 'integer',
            'testValidNegative' => 'integer',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testAlphaWithSpecialChars(): void
    {
        $array = [
            'testError' => 'Bruno@123',
            'testValid' => 'Bruno Çonte Áéíóú',
        ];
        $rules = [
            'testError' => 'alpha',
            'testValid' => 'alpha',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testAlphaNumWithNumbers(): void
    {
        $array = [
            'testError' => 'Bruno@123!',
            'testValid' => 'Bruno 123 Çonte',
        ];
        $rules = [
            'testError' => 'alphaNum',
            'testValid' => 'alphaNum',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testFloatWithNegative(): void
    {
        $array = [
            'testError' => 'abc',
            'testValid' => '-10.5',
            'testValidPositive' => '3.14159',
        ];
        $rules = [
            'testError' => 'float',
            'testValid' => 'float',
            'testValidPositive' => 'float',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testBoolWithDifferentValues(): void
    {
        $array = [
            'testError' => 'invalid',
            'testValidOne' => '1',
            'testValidTrue' => true,
            'testValidYes' => 'yes',
        ];
        $rules = [
            'testError' => 'bool',
            'testValidOne' => 'bool',
            'testValidTrue' => 'bool',
            'testValidYes' => 'bool',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testJsonWithArray(): void
    {
        $array = [
            'testError' => 'not a json',
            'testValidArray' => ['key' => 'value'],
            'testValidString' => '{"name": "Bruno", "idade": 30}',
        ];
        $rules = [
            'testError' => 'json',
            'testValidArray' => 'json',
            'testValidString' => 'json',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testUrlWithDifferentProtocols(): void
    {
        $array = [
            'testError' => 'not-a-url',
            'testValidFtp' => 'ftp://files.example.com',
            'testValidHttp' => 'http://example.com/path',
            'testValidHttps' => 'https://www.example.com',
        ];
        $rules = [
            'testError' => 'url',
            'testValidFtp' => 'url',
            'testValidHttp' => 'url',
            'testValidHttps' => 'url',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testIpWithDifferentFormats(): void
    {
        // @codingStandardsIgnoreStart
        $array = [
            'testError' => '999.999.999.999', // NOSONAR
            'testValidIpv4' => '192.168.1.1', // NOSONAR - Test not actual IP addresses
            'testValidIpv6' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334', // NOSONAR
        ];
        // @codingStandardsIgnoreEnd
        $rules = [
            'testError' => 'ip',
            'testValidIpv4' => 'ip',
            'testValidIpv6' => 'ip',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMacWithDifferentFormats(): void
    {
        $array = [
            'testError' => '00-D0-56-F2-B5',
            'testValidColon' => '00:D0:56:F2:B5:12',
            'testValidDash' => '00-D0-56-F2-B5-12',
        ];
        $rules = [
            'testError' => 'mac',
            'testValidColon' => 'mac',
            'testValidDash' => 'mac',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testZipCodeWithMask(): void
    {
        $array = [
            'testError' => '8704751',
            'testValidNoMask' => '87047510',
            'testValidWithMask' => '87047-510',
        ];
        $rules = [
            'testError' => 'zipcode',
            'testValidNoMask' => 'zipcode',
            'testValidWithMask' => 'zipcode',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testPlateMercosul(): void
    {
        $array = [
            'testErrorFewLetters' => 'AB-1234',
            'testErrorManyDigits' => 'ABC12345',
            'testErrorOnlyDigits' => '1234567',
            'testValidLower' => 'abc-1234',
            'testValidMercosul' => 'ABC1D23',
            'testValidMercosulLower' => 'abc1d23',
            'testValidNoMask' => 'ABC1234',
            'testValidOld' => 'ABC-1234',
        ];
        $rules = [
            'testErrorFewLetters' => 'plate',
            'testErrorManyDigits' => 'plate',
            'testErrorOnlyDigits' => 'plate',
            'testValidLower' => 'plate',
            'testValidMercosul' => 'plate',
            'testValidMercosulLower' => 'plate',
            'testValidNoMask' => 'plate',
            'testValidOld' => 'plate',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testIdentifierOrCompanyAcceptsLowercaseAlphanumericCompany(): void
    {
        $array = [
            'cnpjLower' => 'br.asi.l20/26aa-64',
            'cnpjLowerNoMask' => 'brasil2026aa64',
            'cnpjMixed' => 'Br.Asi.L20/26aa-64',
            'cnpjOtherLower' => 'k7.cm7.10c/0001-84',
            'cnpjUpper' => 'BR.ASI.L20/26AA-64',
        ];
        $rules = [
            'cnpjLower' => 'identifierOrCompany',
            'cnpjLowerNoMask' => 'identifierOrCompany',
            'cnpjMixed' => 'identifierOrCompany',
            'cnpjOtherLower' => 'identifierOrCompany',
            'cnpjUpper' => 'identifierOrCompany',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testDddAcceptsMaskedValue(): void
    {
        $array = [
            'dddError' => '(00)',
            'dddMasked' => '(44)',
            'dddMaskedThreeDigits' => '(011)',
            'dddSpaced' => ' 44 ',
        ];
        $rules = [
            'dddError' => 'ddd',
            'dddMasked' => 'ddd',
            'dddMaskedThreeDigits' => 'ddd',
            'dddSpaced' => 'ddd',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDddByStateAcceptsMaskedValue(): void
    {
        $array = [
            'dddPrError' => '(11)',
            'dddPrValid' => '(44)',
        ];
        $rules = [
            'dddPrError' => 'ddd:pr',
            'dddPrValid' => 'ddd:pr',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testRgbColorAcceptsFunctionNotation(): void
    {
        $array = [
            'rgbError' => 'rgb(256,0,0)',
            'rgbErrorEmpty' => 'rgb()',
            'rgbFunction' => 'rgb(255,255,255)',
            'rgbFunctionSpaced' => 'rgb( 128 , 128 , 128 )',
            'rgbFunctionUpper' => 'RGB(0,0,0)',
        ];
        $rules = [
            'rgbError' => 'rgbColor',
            'rgbErrorEmpty' => 'rgbColor',
            'rgbFunction' => 'rgbColor',
            'rgbFunctionSpaced' => 'rgbColor',
            'rgbFunctionUpper' => 'rgbColor',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testValidatorTrimsSurroundingSpacesBeforeRules(): void
    {
        $array = [
            'cpf' => ' 894.213.600-10 ',
            'ddd' => ' 44 ',
            'plate' => ' ABC-1234 ',
            'zip' => ' 87020-000 ',
        ];
        $rules = [
            'cpf' => 'identifier',
            'ddd' => 'ddd',
            'plate' => 'plate',
            'zip' => 'zipcode',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testZipCodeAcceptsDottedMask(): void
    {
        $array = [
            'zipDotted' => '87.020-000',
            'zipError' => '870200001',
            'zipMasked' => '87020-000',
            'zipNoMask' => '87020000',
            'zipSpaced' => ' 87020-000 ',
        ];
        $rules = [
            'zipDotted' => 'zipcode',
            'zipError' => 'zipcode',
            'zipMasked' => 'zipcode',
            'zipNoMask' => 'zipcode',
            'zipSpaced' => 'zipcode',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testNumericWithDifferentTypes(): void
    {
        $array = [
            'testError' => 'abc',
            'testValidFloat' => '78.90',
            'testValidInt' => 123,
            'testValidNegative' => '-123',
            'testValidString' => '456',
        ];
        $rules = [
            'testError' => 'numeric',
            'testValidFloat' => 'numeric',
            'testValidInt' => 'numeric',
            'testValidNegative' => 'numeric',
            'testValidString' => 'numeric',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateBrazilWithMask(): void
    {
        $array = [
            'testError' => '32/12/2024',
            'testValidNoMask' => '31122024',
            'testValidWithMask' => '31/12/2024',
        ];
        $rules = [
            'testError' => 'dateBrazil',
            'testValidNoMask' => 'dateBrazil',
            'testValidWithMask' => 'dateBrazil',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateAmericanWithMask(): void
    {
        $array = [
            'testError' => '2024-13-01',
            'testValidNoMask' => '20241231',
            'testValidWithMask' => '2024-12-31',
        ];
        $rules = [
            'testError' => 'dateAmerican',
            'testValidNoMask' => 'dateAmerican',
            'testValidWithMask' => 'dateAmerican',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateAmericanWithUnitedStatesFormat(): void
    {
        $array = [
            'testErrorMonth' => '13/31/2024',
            'testErrorDay' => '12/32/2024',
            'testValidEndOfYear' => '12/31/2024',
            'testValidLeapDay' => '02/29/2024',
        ];
        $rules = [
            'testErrorMonth' => 'dateAmerican',
            'testErrorDay' => 'dateAmerican',
            'testValidEndOfYear' => 'dateAmerican',
            'testValidLeapDay' => 'dateAmerican',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testHourWithDifferentFormats(): void
    {
        $array = [
            'testError' => '25:00',
            'testErrorFormat' => '12:60',
            'testValid' => '23:59',
            'testValidMidnight' => '00:00',
        ];
        $rules = [
            'testError' => 'hour',
            'testErrorFormat' => 'hour',
            'testValid' => 'hour',
            'testValidMidnight' => 'hour',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMinMaxCombined(): void
    {
        $array = [
            'testErrorMax' => 'Bruno Conte Developer',
            'testErrorMin' => 'AB',
            'testValid' => 'Bruno',
        ];
        $rules = [
            'testErrorMax' => self::RULE_MIN_3_MAX_10,
            'testErrorMin' => self::RULE_MIN_3_MAX_10,
            'testValid' => self::RULE_MIN_3_MAX_10,
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNumMinMaxCombined(): void
    {
        $array = [
            'testErrorMax' => 150,
            'testErrorMin' => 5,
            'testValid' => 50,
        ];
        $rules = [
            'testErrorMax' => self::RULE_NUM_MIN_10_MAX_100,
            'testErrorMin' => self::RULE_NUM_MIN_10_MAX_100,
            'testValid' => self::RULE_NUM_MIN_10_MAX_100,
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testRegexWithComplexPatterns(): void
    {
        $array = [
            'testError' => '123-45-6789',
            'testValidCep' => '12345-678',
            'testValidPhone' => '(11) 99999-8888',
        ];
        $rules = [
            'testError' => 'regex:/^\d{5}-\d{3}$/',
            'testValidCep' => 'regex:/^\d{5}-\d{3}$/',
            'testValidPhone' => 'regex:/^\(\d{2}\) \d{5}-\d{4}$/',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testEqualsWithSameValues(): void
    {
        $array = [
            'confirmPassword' => 'secret123',
            'password' => 'secret123',
        ];
        $rules = [
            'confirmPassword' => 'required|min:6|equals:password',
            'password' => 'required|min:6',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testLowerWithMixedChars(): void
    {
        $array = [
            'testError' => 'Texto Com Maiúsculo',
            'testValid' => 'texto todo minúsculo',
        ];
        $rules = [
            'testError' => 'lower',
            'testValid' => 'lower',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testUpperWithMixedChars(): void
    {
        $array = [
            'testError' => 'Texto com Minúsculo',
            'testValid' => 'TEXTO TODO MAIÚSCULO',
        ];
        $rules = [
            'testError' => 'upper',
            'testValid' => 'upper',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testRequiredWithZeroValue(): void
    {
        $array = [
            'testEmpty' => '',
            'testValidZeroInt' => 0,
            'testValidZeroString' => '0',
        ];
        $rules = [
            'testEmpty' => 'required',
            'testValidZeroInt' => 'required',
            'testValidZeroString' => 'required',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testArrayValuesWithMultipleOptions(): void
    {
        $array = [
            'testError' => 'X',
            'testValidN' => 'N',
            'testValidS' => 'S',
            'testValidT' => 'T',
        ];
        $rules = [
            'testError' => self::RULE_ARRAY_VALUES,
            'testValidN' => self::RULE_ARRAY_VALUES,
            'testValidS' => self::RULE_ARRAY_VALUES,
            'testValidT' => self::RULE_ARRAY_VALUES,
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testOptionalWithValidValue(): void
    {
        $array = [
            'testOptionalEmpty' => '',
            'testOptionalNull' => null,
            'testOptionalValid' => 'Bruno',
        ];
        $rules = [
            'testOptionalEmpty' => self::RULE_OPTIONAL_MIN_3,
            'testOptionalNull' => self::RULE_OPTIONAL_MIN_3,
            'testOptionalValid' => self::RULE_OPTIONAL_MIN_3,
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testOptionalWithInvalidValue(): void
    {
        $array = [
            'testOptionalInvalid' => 'AB',
        ];
        $rules = [
            'testOptionalInvalid' => self::RULE_OPTIONAL_MIN_5,
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testCustomMessageWithMultipleRules(): void
    {
        $customMsg = 'Este campo é inválido, verifique os requisitos!';
        $array = [
            'field' => 'ab',
        ];
        $rules = [
            'field' => self::RULE_REQUIRED_MIN_5 . $customMsg,
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertEquals($customMsg, $validator->getErros()['field']);
    }

    public function testMinWordsWithExactMatch(): void
    {
        $array = [
            'testExact' => self::VALUE_FULL_NAME,
            'testLess' => 'Bruno',
            'testMore' => 'Bruno Conte Developer PHP',
        ];
        $rules = [
            'testExact' => self::RULE_MIN_WORDS_2,
            'testLess' => self::RULE_MIN_WORDS_2,
            'testMore' => self::RULE_MIN_WORDS_2,
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMaxWordsWithExactMatch(): void
    {
        $array = [
            'testExact' => self::VALUE_FULL_NAME,
            'testLess' => 'Bruno',
            'testMore' => 'Bruno Conte Developer PHP',
        ];
        $rules = [
            'testExact' => self::RULE_MAX_WORDS_2,
            'testLess' => self::RULE_MAX_WORDS_2,
            'testMore' => self::RULE_MAX_WORDS_2,
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testNumMonthBoundaries(): void
    {
        $array = [
            'testErrorNegative' => -1,
            'testErrorThirteen' => 13,
            'testErrorZero' => 0,
            'testValidOne' => 1,
            'testValidTwelve' => 12,
        ];
        $rules = [
            'testErrorNegative' => 'numMonth',
            'testErrorThirteen' => 'numMonth',
            'testErrorZero' => 'numMonth',
            'testValidOne' => 'numMonth',
            'testValidTwelve' => 'numMonth',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testIdentifierWithAllZeros(): void
    {
        $array = [
            'testError' => '000.000.000-00',
            'testValid' => '556.344.058-31',
        ];
        $rules = [
            'testError' => 'identifier',
            'testValid' => 'identifier',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testCompanyIdentificationWithAllZeros(): void
    {
        $array = [
            'testError' => '00.000.000/0000-00',
            'testValid' => '21.111.527/0001-63',
        ];
        $rules = [
            'testError' => 'companyIdentification',
            'testValid' => 'companyIdentification',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testTimestampWithDifferentFormats(): void
    {
        $array = [
            'testErrorInvalidTime' => '2024-12-31 25:00:00',
            'testErrorNoSeconds' => '2024-12-31 23:59',
            'testValidAmerican' => '2024-12-31 23:59:59',
            'testValidBrazil' => '31/12/2024 23:59:59',
        ];
        $rules = [
            'testErrorInvalidTime' => 'timestamp',
            'testErrorNoSeconds' => 'timestamp',
            'testValidAmerican' => 'timestamp',
            'testValidBrazil' => 'timestamp',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNoWeekendWithDifferentFormats(): void
    {
        $nextMonday = date(self::FORMAT_DATE_BRAZIL, strtotime('next monday'));
        $nextSaturday = date(self::FORMAT_DATE_BRAZIL, strtotime('next saturday'));
        $nextSunday = date(self::FORMAT_DATE_BRAZIL, strtotime('next sunday'));
        $array = [
            'testErrorSaturday' => $nextSaturday,
            'testErrorSunday' => $nextSunday,
            'testValidWeekday' => $nextMonday,
        ];
        $rules = [
            'testErrorSaturday' => 'noWeekend',
            'testErrorSunday' => 'noWeekend',
            'testValidWeekday' => 'noWeekend',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNotSpaceWithMultipleSpaces(): void
    {
        // Espaços no início e fim são removidos durante o processamento (trim/sanitização)
        // Apenas espaços no meio da string são detectados pela regra notSpace
        $array = [
            'testErrorDouble' => 'Bruno  Conte',
            'testErrorMiddle' => self::VALUE_FULL_NAME,
            'testErrorMultiple' => 'Bruno Conte Developer',
            'testValid' => 'BrunoConteDevelope',
        ];
        $rules = [
            'testErrorDouble' => 'notSpace',
            'testErrorMiddle' => 'notSpace',
            'testErrorMultiple' => 'notSpace',
            'testValid' => 'notSpace',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testDddWithInvalidValues(): void
    {
        $array = [
            'testErrorFourDigits' => '1234',
            'testErrorSingleDigit' => '1',
            'testValid' => '11',
            'testValidThreeDigits' => '011',
        ];
        $rules = [
            'testErrorFourDigits' => 'ddd',
            'testErrorSingleDigit' => 'ddd',
            'testValid' => 'ddd',
            'testValidThreeDigits' => 'ddd',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testRgbColorBoundaries(): void
    {
        $array = [
            'testErrorNegative' => '-1, 0, 0',
            'testErrorOver' => '256, 0, 0',
            'testValidMax' => '255, 255, 255',
            'testValidMid' => '128, 128, 128',
            'testValidMin' => '0, 0, 0',
        ];
        $rules = [
            'testErrorNegative' => 'rgbColor',
            'testErrorOver' => 'rgbColor',
            'testValidMax' => 'rgbColor',
            'testValidMid' => 'rgbColor',
            'testValidMin' => 'rgbColor',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testJsonParamWithMultipleRules(): void
    {
        $array = [
            'field' => 'Bruno123',
        ];
        $rules = [
            'field' => '{"required":"true","type":"alphaNum","min":"5","max":"20"}',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testJsonParamWithInvalidValue(): void
    {
        $array = [
            'field' => 'AB',
        ];
        $rules = [
            'field' => '{"required":"true","type":"alphaNum","min":"5"}',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testFileNameWithValidFiles(): void
    {
        $fileValid = [
            'error' => 0,
            'name' => 'valid-file-name.pdf',
            'size' => 1024,
            'tmp_name' => '/tmp/phpTest',
            'type' => self::MIME_PDF,
        ];
        $array = ['file' => $fileValid];
        $rules = ['file' => 'fileName'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertFalse(!empty($validator->getErros()));
    }

    public function testMaxWithUnicodeChars(): void
    {
        $array = [
            'testError' => 'Açúcar doce especial',
            'testValid' => 'Açúcar',
        ];
        $rules = [
            'testError' => 'max:10',
            'testValid' => 'max:10',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMinWithUnicodeChars(): void
    {
        $array = [
            'testError' => 'Açú',
            'testValid' => 'Açúcar',
        ];
        $rules = [
            'testError' => self::RULE_MIN_5,
            'testValid' => self::RULE_MIN_5,
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testEmailWithInvalidFormats(): void
    {
        $array = [
            'testErrorNoAt' => 'testexample.com',
            'testErrorNoDomain' => 'test@',
            'testErrorNoUser' => '@example.com',
            'testValid' => 'test@example.com',
        ];
        $rules = [
            'testErrorNoAt' => 'email',
            'testErrorNoDomain' => 'email',
            'testErrorNoUser' => 'email',
            'testValid' => 'email',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testPhoneWithDifferentLengths(): void
    {
        $array = [
            'testError9' => '113333444',
            'testError12' => '119999988880',
            'testValid10' => '1133334444',
            'testValid11' => '11999998888',
        ];
        $rules = [
            'testError9' => 'phone',
            'testError12' => 'phone',
            'testValid10' => 'phone',
            'testValid11' => 'phone',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testUnknownRuleReportsInvalidRuleError(): void
    {
        $validator = $this->validate(['field' => 'abc'], ['field' => 'ruleThatDoesNotExist']);

        self::assertSame(
            ['field' => 'Uma regra inválida está sendo aplicada no campo field!'],
            $validator->getErros(),
        );
    }

    public function testUnknownRuleWithListValueReportsInvalidRuleError(): void
    {
        $validator = $this->validate(['field' => 'abc'], ['field' => 'ruleThatDoesNotExist:a;b']);

        self::assertSame(
            ['field' => 'Uma regra inválida está sendo aplicada no campo field!'],
            $validator->getErros(),
        );
    }
}
