<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitRuleTest extends TestCase
{
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
            'name'     => 'fileUpload ',
            'type'     => 'image/jpeg',
            'tmp_name' => $archive,
            'error'    => 0,
            'size'     => 19639,
        ];
    }

    private function mountFileDataRequired(): array
    {
        return [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => 4,
            'size' => 0,
        ];
    }

    private function mountMineTypeFile(int $size = 8488): array
    {
        return [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => $size,
        ];
    }

    public function testArray(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a', 'testValid' => ['a' => 1, 'b' => 2]],
            ['testError' => 'array', 'testValid' => 'array']
        );
    }

    public function testArrayValues(): void
    {
        $array = ['testError' => 'M', 'testValid' => 'S',];
        $rules = ['testError' => 'arrayValues:S-N-T', 'testValid' => 'arrayValues:S-N-T',];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        $array = [
            'dadosArrayErro' => 11,
            'arrayVazioErro' => [],
            'dadosArrayRequired' => ['empresa' => 'cooper'],
        ];
        $rules = [
            'dadosArrayErro' => 'array',
            'arrayVazioErro' => 'required',
            'dadosArrayRequired' => 'required|array',
        ];
        $expected = [
            'dadosArrayErro' => 'A variável dadosArrayErro não é um array!',
            'arrayVazioErro' => 'O campo arrayVazioErro é obrigatório!',
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
            ['testError' => 'int', 'testValid' => 'bool']
        );
    }

    public function testCompanyIdentification(): void
    {
        $array = [
            'testError' => '52186923000120',
            'testErrorEmpty' => '',
            'testValid' => '21111527000163',
            'testOtherValid' => 'JA.JL4.X24/9VI6-23',
            'testExceptionError' => '12123456000712',
            'testExceptionValid' => '00000000000000',
        ];
        $rules = [
            'testError' => 'companyIdentification',
            'testErrorEmpty' => 'companyIdentification',
            'testValid' => 'companyIdentification',
            'testOtherValid' => 'companyIdentification',
            'testExceptionError' => 'companyIdentification:12123456000712',
            'testExceptionValid' => 'companyIdentification:00000000000000;22222222222222',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testDateAmerican(): void
    {
        self::assertErrorCount(
            2,
            ['testError' => '1990-04-31', 'testErrorEmpty' => '', 'testValid' => '1990-04-30'],
            ['testError' => 'dateAmerican', 'testErrorEmpty' => 'dateAmerican', 'testValid' => 'dateAmerican']
        );
    }

    public function testDateBrazil(): void
    {
        self::assertErrorCount(
            2,
            ['testError' => '31042020', 'testErrorEmpty' => '', 'testValid' => '31052020'],
            ['testError' => 'dateBrazil', 'testErrorEmpty' => 'dateBrazil', 'testValid' => 'dateBrazil']
        );
    }

    public function testEmail(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'bruno.com', 'testValid' => 'brunoconte3@gmail.com'],
            ['testError' => 'email', 'testValid' => 'email']
        );
    }

    public function testIdentifier(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '06669987788', 'testValid' => '55634405831'],
            ['testError' => 'identifier', 'testValid' => 'identifier']
        );
    }

    public function testInt(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a123', 'testValid' => 123],
            ['testError' => 'int', 'testValid' => 'int']
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
            ['testError' => 'float', 'testValid' => 'float']
        );
    }

    public function testHour(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '24:03', 'testValid' => '21:03'],
            ['testError' => '{"type":"hour"}', 'testValid' => '{"type":"hour"}']
        );
    }

    public function testLower(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Abcdção', 'testValid' => 'abcdção'],
            ['testError' => '{"type":"lower"}', 'testValid' => '{"type":"lower"}']
        );
    }

    public function testMac(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '00:00', 'testValid' => '00-D0-56-F2-B5-12'],
            ['testError' => 'mac', 'testValid' => 'mac']
        );
    }

    public function testMax(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 123, 'testValid' => "Avenida Pedra D'Água"],
            ['testError' => 'max:2', 'testValid' => 'max:20']
        );
    }

    public function testMaxWords(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Jorge da Silva', 'testValid' => 'Bruno Conte'],
            ['testError' => 'maxWords:2', 'testValid' => 'maxWords:2']
        );
    }

    public function testMin(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '123', 'testValid' => "Avenida Pedra D'Água"],
            ['testError' => 'min:5', 'testValid' => 'min:20']
        );
    }

    public function testMinWords(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'Jorge da Silva', 'testValid' => 'Bruno Conte'],
            ['testError' => 'minWords:4', 'testValid' => 'minWords:2']
        );
    }

    public function testNoWeekend(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '10/10/2020', 'testValid' => '16/10/2020'],
            ['testError' => 'noWeekend', 'testValid' => 'noWeekend']
        );
    }

    public function testNumeric(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'a', 'testValid' => 123],
            ['testError' => 'numeric', 'testValid' => 'numeric']
        );
    }

    public function testNumMax(): void
    {
        $array = [
            'testValid' => 31,
            'testError' => 32,
            'testErrorMaxZero' => '2',
            'testErrorNegative' => -1,
        ];
        $rules = [
            'testValid' => 'numMax:31',
            'testError' => 'numMax:31',
            'testErrorMaxZero' => 'numMax:0',
            'testErrorNegative' => 'numMax:3',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testNumMin(): void
    {
        $array = [
            'testError' => 2,
            'testeErrorNoInt' => 'a',
            'testeErrorNegative' => '-2',
            'testValid' => 8,
            'testValidZero' => '0',
        ];
        $rules = [
            'testError' => 'numMin:5',
            'testeErrorNoInt' => 'numMin:5',
            'testeErrorNegative' => 'numMin:-2',
            'testValid' => 'numMin:5',
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
            'testValid' => '4433467847',
            'testMask' => '(44) 99932-5847',
            'testInvalidRule' => 'br',
        ];
        $rules = [
            'testError' => 'phone',
            'testValid' => 'phone',
            'testMask' => 'phone',
            'testInvalidRule' => 'naoExisteEssaRegra',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testPlate(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'aXI3668', 'testValid' => 'AXI-3668'],
            ['testError' => 'plate', 'testValid' => 'plate']
        );
    }

    public function testRegEx(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'bruno_conte3', 'testValid' => 'Bruno Conte'],
            ['testError' => 'regex:/^[a-zA-Z\s]+$/', 'testValid' => 'regex:/^[a-zA-Z\s]+$/']
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
        foreach ($array as $key => $valor) {
            $rules[$key] = 'required';
        }

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(5, $validator->getErros());
    }

    public function testType(): void
    {
        $array = [
            'testAlphaError'             => 'Ele usa um dicionário com mais de 200 palavras!',
            'testAlphaNoSpecialError'    => 'Ele usa um dicionário com mais de 200 palavras!',
            'testAlphaNumError'          => 'Ele usa um dicionário com mais de 200 palavras!',
            'testAlphaNumNoSpecialError' => 'Ele usa um dicionário com mais de 200 palavras!',
            'testAlphaValid'             => 'Ele usa um dicionário com mais de X palavras',
            'testAlphaNoSpecialValid'    => 'Ele usa um dicionario com mais de X palavras',
            'testAlphaNumValid'          => 'Ele usa um dicionário com mais de 200 palavras',
            'testAlphaNumNoSpecialValid' => 'Ele usa um dicionario com mais de 200 palavras',
        ];
        $rules = [
            'testAlphaError'             => 'type:alpha',
            'testAlphaNoSpecialError'    => 'type:alphaNoSpecial',
            'testAlphaNumError'          => 'type:alphaNum',
            'testAlphaNumNoSpecialError' => 'type:alphaNumNoSpecial',
            'testAlphaValid'             => 'type:alpha',
            'testAlphaNoSpecialValid'    => 'type:alphaNoSpecial',
            'testAlphaNumValid'          => 'type:alphaNum',
            'testAlphaNumNoSpecialValid' => 'type:alphaNumNoSpecial',
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
            ['testError' => 'upper', 'testValid' => 'upper']
        );
    }

    public function testUrl(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => 'ww.test.c', 'testValid' => 'https://www.google.com.br'],
            ['testError' => 'url', 'testValid' => 'url']
        );
    }

    public function testZipcode(): void
    {
        self::assertErrorCount(
            1,
            ['testError' => '870475', 'testValid' => '87047510'],
            ['testError' => 'zipcode', 'testValid' => 'zipcode']
        );
    }

    public function testCustomMessage(): void
    {
        $msg = 'Mensagem customizada aqui, devendo conter no mínimo uma vírgula!';
        $array = [
            'textoError' => 'abc',
            'textoValid' => 'abcde',
        ];
        $rules = [
            'textoError' => 'required|min:5, ' . $msg . '|max:20',
            'textoValid' => 'required|min:5, ' . $msg . '|max:20',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertEquals($msg, $validator->getErros()['textoError']);
    }

    public function testNotSpace(): void
    {
        self::assertErrorCount(
            1,
            ['validarEspacoError' => 'BRU C', 'validarEspacoValid' => 'BRUC'],
            ['validarEspacoError' => 'notSpace', 'validarEspacoValid' => 'notSpace']
        );
    }

    public function testJson(): void
    {
        self::assertErrorCount(
            1,
            ['validaJsonError' => '"nome": "Bruno"}', 'validaJsonValid' => '{"nome": "Bruno"}'],
            ['validaJsonError' => 'type:json', 'validaJsonValid' => 'type:json']
        );
    }

    public function testNumMonth(): void
    {
        self::assertErrorCount(
            1,
            ['validaMesError' => 13, 'validaMesValid' => 10],
            ['validaMesError' => 'numMonth', 'validaMesValid' => 'numMonth']
        );
    }

    public function testIdentifierOrCompany(): void
    {
        $array = [
            'cpfOuCnpjerror' => '9E.2A4.092.0001/5A',
            'cpfOuCnpjValid' => 'DE.VUT.ILS/123X-49',
            'cpfOuCnpjExceptionError' => '12.123.456/0007-12',
            'cpfOuCnpjExceptionValid' => '00.000.000/0000-00',
            'cpfOuCnpjInvalid' => '0966894790',
        ];
        $rules = [
            'cpfOuCnpjerror' => 'identifierOrCompany',
            'cpfOuCnpjValid' => 'identifierOrCompany',
            'cpfOuCnpjExceptionError' => 'identifierOrCompany:12123456000712',
            'cpfOuCnpjExceptionValid' => 'identifierOrCompany:00000000000000;22222222222222',
            'cpfOuCnpjInvalid' => 'identifierOrCompany',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testFileMaxUploadSize(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile();
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf',],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL',],
            'error'    => ['0' => 0, '1' => 0,],
            'size'     => ['0' => 8488, '1' => 818465,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'maxUploadSize:5550',
            'fileUploadMultiple' => 'maxUploadSize:5550',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileMinUploadSize(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile(3589);
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf',],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL',],
            'error'    => ['0' => 0, '1' => 0,],
            'size'     => ['0' => 4450, '1' => 4980,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'minUploadSize:5550',
            'fileUploadMultiple' => 'minUploadSize:5550',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileMimeType(): void
    {
        $fileUploadSingle = $this->mountMineTypeFile();
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf',],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL',],
            'error'    => ['0' => 0, '1' => 0,],
            'size'     => ['0' => 8488, '1' => 818465,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'mimeType:jpeg;png',
            'fileUploadMultiple' => 'mimeType:png;svg',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testFileName(): void
    {
        $fileUploadSingle = [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 8488,
        ];
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf',],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL',],
            'error'    => ['0' => 0, '1' => 0,],
            'size'     => ['0' => 8488, '1' => 818465,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'fileName',
            'fileUploadMultiple' => 'fileName',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertFalse(!empty($validator->getErros()));
    }

    public function testRequiredFile(): void
    {
        $fileUploadSingle = $this->mountFileDataRequired();
        $fileUploadMultiple = [
            'name'     => ['0' => '',],
            'type'     => ['0' => '',],
            'tmp_name' => ['0' => '',],
            'error'    => ['0' => 4,],
            'size'     => ['0' => 0,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'requiredFile',
            'fileUploadMultiple' => 'requiredFile',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMaxFile(): void
    {
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf',],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL',],
            'error'    => ['0' => 0, '1' => 0,],
            'size'     => ['0' => 8488, '1' => 818465,],
        ];
        $array = ['fileUploadMultiple' => $fileUploadMultiple];
        self::assertErrorCount(1, $array, ['fileUploadMultiple' => 'maxFile:1']);
    }

    public function testMinFile(): void
    {
        $fileUploadSingle = $this->mountFileDataRequired();
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg',],
            'type'     => ['0' => 'image/jpeg',],
            'tmp_name' => ['0' => '/tmp/phpODnLGo',],
            'error'    => ['0' => 0,],
            'size'     => ['0' => 8488,],
        ];
        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];
        $rules = [
            'fileUploadSingle' => 'minFile:1',
            'fileUploadMultiple' => 'minFile:2',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testTimestamp(): void
    {
        $array = [
            'dateHourAmericanError' => '2021-04-15 21:01',
            'dateHourAmericanValid' => '2021-04-15 21:01:04',
            'dateHourBrasilError' => '15/04/2021 21:01',
            'dateHourBrasilValid' => '15/04/2021 21:01:04',
        ];
        $rules = [
            'dateHourAmericanError' => 'timestamp',
            'dateHourAmericanValid' => 'timestamp',
            'dateHourBrasilError' => 'timestamp',
            'dateHourBrasilValid' => 'timestamp',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testEquals(): void
    {
        $array = [
            'senha' => 'mudar',
            'confirmarSenha' => 'mudar123',
            'regraInvalida' => 'teste',
        ];
        $rules = [
            'senha' => 'min:3|max:5|alpha',
            'confirmarSenha' => 'min:3|max:5|alpha|equals:senha',
            'regraInvalida' => 'equals',
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
}
