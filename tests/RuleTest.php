<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
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

    public function testDateNotFuture(): void
    {
        $futureDate = date('d/m/Y', strtotime('+1 year'));
        $pastDate = date('d/m/Y', strtotime('-1 year'));
        $today = date('d/m/Y');
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
            'dddPrValid' => '44',
            'dddPrError' => '11',
            'dddSpValid' => '11',
        ];
        $rules = [
            'dddPrValid' => 'ddd:pr',
            'dddPrError' => 'ddd:pr',
            'dddSpValid' => 'ddd:sp',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testRgbColor(): void
    {
        $array = [
            'rgbError' => '300, 100, 50',
            'rgbValid' => '255, 100, 50',
            'rgbValidNoSpaces' => '0,0,0',
            'rgbValidMax' => '255,255,255',
            'rgbInvalidNegative' => '-1, 100, 50',
        ];
        $rules = [
            'rgbError' => 'rgbColor',
            'rgbValid' => 'rgbColor',
            'rgbValidNoSpaces' => 'rgbColor',
            'rgbValidMax' => 'rgbColor',
            'rgbInvalidNegative' => 'rgbColor',
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
        $validator->set([], ['campo' => 'required']);
        self::assertArrayHasKey('erro', $validator->getErros());
    }

    public function testValidatorWithNestedArray(): void
    {
        $array = [
            ['nome' => 'Bruno', 'idade' => 30],
            ['nome' => '', 'idade' => 25],
        ];
        $rules = [
            'nome' => 'required|min:2',
            'idade' => 'required|int',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testOptionalWithValue(): void
    {
        $array = ['campo' => 'ab'];
        $rules = ['campo' => 'optional|min:5'];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testOptionalWithEmptyValue(): void
    {
        $array = ['campo' => ''];
        $rules = ['campo' => 'optional|min:5'];
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
            'cpfValid' => '556.344.058-31',
            'cpfInvalid' => '111.111.111-11',
        ];
        $rules = [
            'cpfValid' => 'identifier',
            'cpfInvalid' => 'identifier',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testCompanyIdentificationWithMask(): void
    {
        $array = [
            'cnpjValid' => '21.111.527/0001-63',
            'cnpjInvalid' => '11.111.111/1111-11',
        ];
        $rules = [
            'cnpjValid' => 'companyIdentification',
            'cnpjInvalid' => 'companyIdentification',
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
            'testValid' => '-10.5',
            'testValidPositive' => '3.14159',
            'testError' => 'abc',
        ];
        $rules = [
            'testValid' => 'float',
            'testValidPositive' => 'float',
            'testError' => 'float',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testBoolWithDifferentValues(): void
    {
        $array = [
            'testValidTrue' => true,
            'testValidOne' => '1',
            'testValidYes' => 'yes',
            'testError' => 'invalid',
        ];
        $rules = [
            'testValidTrue' => 'bool',
            'testValidOne' => 'bool',
            'testValidYes' => 'bool',
            'testError' => 'bool',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testJsonWithArray(): void
    {
        $array = [
            'testValidArray' => ['key' => 'value'],
            'testValidString' => '{"nome": "Bruno", "idade": 30}',
            'testError' => 'not a json',
        ];
        $rules = [
            'testValidArray' => 'json',
            'testValidString' => 'json',
            'testError' => 'json',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testUrlWithDifferentProtocols(): void
    {
        $array = [
            'testValidHttps' => 'https://www.example.com',
            'testValidHttp' => 'http://example.com/path',
            'testValidFtp' => 'ftp://files.example.com',
            'testError' => 'not-a-url',
        ];
        $rules = [
            'testValidHttps' => 'url',
            'testValidHttp' => 'url',
            'testValidFtp' => 'url',
            'testError' => 'url',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testIpWithDifferentFormats(): void
    {
        // @codingStandardsIgnoreStart
        $array = [
            'testValidIpv4' => '192.168.1.1', // NOSONAR - Test not actual IP addresses
            'testValidIpv6' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334', // NOSONAR
            'testError' => '999.999.999.999', // NOSONAR
        ];
        // @codingStandardsIgnoreEnd
        $rules = [
            'testValidIpv4' => 'ip',
            'testValidIpv6' => 'ip',
            'testError' => 'ip',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMacWithDifferentFormats(): void
    {
        $array = [
            'testValidDash' => '00-D0-56-F2-B5-12',
            'testValidColon' => '00:D0:56:F2:B5:12',
            'testError' => '00-D0-56-F2-B5',
        ];
        $rules = [
            'testValidDash' => 'mac',
            'testValidColon' => 'mac',
            'testError' => 'mac',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testZipcodeWithMask(): void
    {
        $array = [
            'testValidWithMask' => '87047-510',
            'testValidNoMask' => '87047510',
            'testError' => '8704751',
        ];
        $rules = [
            'testValidWithMask' => 'zipcode',
            'testValidNoMask' => 'zipcode',
            'testError' => 'zipcode',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testPlateMercosul(): void
    {
        $array = [
            'testValidOld' => 'ABC-1234',
            'testErrorMercosul' => 'ABC1D23',
            'testErrorLower' => 'abc-1234',
        ];
        $rules = [
            'testValidOld' => 'plate',
            'testErrorMercosul' => 'plate',
            'testErrorLower' => 'plate',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNumericWithDifferentTypes(): void
    {
        $array = [
            'testValidInt' => 123,
            'testValidString' => '456',
            'testValidFloat' => '78.90',
            'testValidNegative' => '-123',
            'testError' => 'abc',
        ];
        $rules = [
            'testValidInt' => 'numeric',
            'testValidString' => 'numeric',
            'testValidFloat' => 'numeric',
            'testValidNegative' => 'numeric',
            'testError' => 'numeric',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateBrazilWithMask(): void
    {
        $array = [
            'testValidWithMask' => '31/12/2024',
            'testValidNoMask' => '31122024',
            'testError' => '32/12/2024',
        ];
        $rules = [
            'testValidWithMask' => 'dateBrazil',
            'testValidNoMask' => 'dateBrazil',
            'testError' => 'dateBrazil',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testDateAmericanWithMask(): void
    {
        $array = [
            'testValidWithMask' => '2024-12-31',
            'testValidNoMask' => '20241231',
            'testError' => '2024-13-01',
        ];
        $rules = [
            'testValidWithMask' => 'dateAmerican',
            'testValidNoMask' => 'dateAmerican',
            'testError' => 'dateAmerican',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testHourWithDifferentFormats(): void
    {
        $array = [
            'testValid' => '23:59',
            'testValidMidnight' => '00:00',
            'testError' => '25:00',
            'testErrorFormat' => '12:60',
        ];
        $rules = [
            'testValid' => 'hour',
            'testValidMidnight' => 'hour',
            'testError' => 'hour',
            'testErrorFormat' => 'hour',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testMinMaxCombined(): void
    {
        $array = [
            'testValid' => 'Bruno',
            'testErrorMin' => 'AB',
            'testErrorMax' => 'Bruno Conte Developer',
        ];
        $rules = [
            'testValid' => 'min:3|max:10',
            'testErrorMin' => 'min:3|max:10',
            'testErrorMax' => 'min:3|max:10',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNumMinMaxCombined(): void
    {
        $array = [
            'testValid' => 50,
            'testErrorMin' => 5,
            'testErrorMax' => 150,
        ];
        $rules = [
            'testValid' => 'numMin:10|numMax:100',
            'testErrorMin' => 'numMin:10|numMax:100',
            'testErrorMax' => 'numMin:10|numMax:100',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testRegexWithComplexPatterns(): void
    {
        $array = [
            'testValidCep' => '12345-678',
            'testValidPhone' => '(11) 99999-8888',
            'testError' => '123-45-6789',
        ];
        $rules = [
            'testValidCep' => 'regex:/^\d{5}-\d{3}$/',
            'testValidPhone' => 'regex:/^\(\d{2}\) \d{5}-\d{4}$/',
            'testError' => 'regex:/^\d{5}-\d{3}$/',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testEqualsWithSameValues(): void
    {
        $array = [
            'password' => 'secret123',
            'confirmPassword' => 'secret123',
        ];
        $rules = [
            'password' => 'required|min:6',
            'confirmPassword' => 'required|min:6|equals:password',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testLowerWithMixedChars(): void
    {
        $array = [
            'testValid' => 'texto todo minúsculo',
            'testError' => 'Texto Com Maiúsculo',
        ];
        $rules = [
            'testValid' => 'lower',
            'testError' => 'lower',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testUpperWithMixedChars(): void
    {
        $array = [
            'testValid' => 'TEXTO TODO MAIÚSCULO',
            'testError' => 'Texto com Minúsculo',
        ];
        $rules = [
            'testValid' => 'upper',
            'testError' => 'upper',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testRequiredWithZeroValue(): void
    {
        $array = [
            'testValidZeroString' => '0',
            'testValidZeroInt' => 0,
            'testEmpty' => '',
        ];
        $rules = [
            'testValidZeroString' => 'required',
            'testValidZeroInt' => 'required',
            'testEmpty' => 'required',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testArrayValuesWithMultipleOptions(): void
    {
        $array = [
            'testValidS' => 'S',
            'testValidN' => 'N',
            'testValidT' => 'T',
            'testError' => 'X',
        ];
        $rules = [
            'testValidS' => 'arrayValues:S-N-T',
            'testValidN' => 'arrayValues:S-N-T',
            'testValidT' => 'arrayValues:S-N-T',
            'testError' => 'arrayValues:S-N-T',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testOptionalWithValidValue(): void
    {
        $array = [
            'testOptionalValid' => 'Bruno',
            'testOptionalEmpty' => '',
            'testOptionalNull' => null,
        ];
        $rules = [
            'testOptionalValid' => 'optional|min:3',
            'testOptionalEmpty' => 'optional|min:3',
            'testOptionalNull' => 'optional|min:3',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testOptionalWithInvalidValue(): void
    {
        $array = [
            'testOptionalInvalid' => 'AB',
        ];
        $rules = [
            'testOptionalInvalid' => 'optional|min:5',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testCustomMessageWithMultipleRules(): void
    {
        $customMsg = 'Este campo é inválido, verifique os requisitos!';
        $array = [
            'campo' => 'ab',
        ];
        $rules = [
            'campo' => 'required|min:5, ' . $customMsg,
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
        self::assertEquals($customMsg, $validator->getErros()['campo']);
    }

    public function testMinWordsWithExactMatch(): void
    {
        $array = [
            'testExact' => 'Bruno Conte',
            'testMore' => 'Bruno Conte Developer PHP',
            'testLess' => 'Bruno',
        ];
        $rules = [
            'testExact' => 'minWords:2',
            'testMore' => 'minWords:2',
            'testLess' => 'minWords:2',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMaxWordsWithExactMatch(): void
    {
        $array = [
            'testExact' => 'Bruno Conte',
            'testLess' => 'Bruno',
            'testMore' => 'Bruno Conte Developer PHP',
        ];
        $rules = [
            'testExact' => 'maxWords:2',
            'testLess' => 'maxWords:2',
            'testMore' => 'maxWords:2',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testNumMonthBoundaries(): void
    {
        $array = [
            'testValidOne' => 1,
            'testValidTwelve' => 12,
            'testErrorZero' => 0,
            'testErrorThirteen' => 13,
            'testErrorNegative' => -1,
        ];
        $rules = [
            'testValidOne' => 'numMonth',
            'testValidTwelve' => 'numMonth',
            'testErrorZero' => 'numMonth',
            'testErrorThirteen' => 'numMonth',
            'testErrorNegative' => 'numMonth',
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
            'testValidAmerican' => '2024-12-31 23:59:59',
            'testValidBrazil' => '31/12/2024 23:59:59',
            'testErrorNoSeconds' => '2024-12-31 23:59',
            'testErrorInvalidTime' => '2024-12-31 25:00:00',
        ];
        $rules = [
            'testValidAmerican' => 'timestamp',
            'testValidBrazil' => 'timestamp',
            'testErrorNoSeconds' => 'timestamp',
            'testErrorInvalidTime' => 'timestamp',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNoWeekendWithDifferentFormats(): void
    {
        $nextMonday = date('d/m/Y', strtotime('next monday'));
        $nextSaturday = date('d/m/Y', strtotime('next saturday'));
        $nextSunday = date('d/m/Y', strtotime('next sunday'));
        $array = [
            'testValidWeekday' => $nextMonday,
            'testErrorSaturday' => $nextSaturday,
            'testErrorSunday' => $nextSunday,
        ];
        $rules = [
            'testValidWeekday' => 'noWeekend',
            'testErrorSaturday' => 'noWeekend',
            'testErrorSunday' => 'noWeekend',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testNotSpaceWithMultipleSpaces(): void
    {
        // Espaços no início e fim são removidos durante o processamento (trim/sanitização)
        // Apenas espaços no meio da string são detectados pela regra notSpace
        $array = [
            'testValid' => 'BrunoConteDevelope',
            'testErrorMiddle' => 'Bruno Conte',
            'testErrorMultiple' => 'Bruno Conte Developer',
            'testErrorDouble' => 'Bruno  Conte',
        ];
        $rules = [
            'testValid' => 'notSpace',
            'testErrorMiddle' => 'notSpace',
            'testErrorMultiple' => 'notSpace',
            'testErrorDouble' => 'notSpace',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testDddWithInvalidValues(): void
    {
        $array = [
            'testValid' => '11',
            'testValidThreeDigits' => '011',
            'testErrorSingleDigit' => '1',
            'testErrorFourDigits' => '1234',
        ];
        $rules = [
            'testValid' => 'ddd',
            'testValidThreeDigits' => 'ddd',
            'testErrorSingleDigit' => 'ddd',
            'testErrorFourDigits' => 'ddd',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testRgbColorBoundaries(): void
    {
        $array = [
            'testValidMin' => '0, 0, 0',
            'testValidMax' => '255, 255, 255',
            'testValidMid' => '128, 128, 128',
            'testErrorOver' => '256, 0, 0',
            'testErrorNegative' => '-1, 0, 0',
        ];
        $rules = [
            'testValidMin' => 'rgbColor',
            'testValidMax' => 'rgbColor',
            'testValidMid' => 'rgbColor',
            'testErrorOver' => 'rgbColor',
            'testErrorNegative' => 'rgbColor',
        ];
        self::assertErrorCount(2, $array, $rules);
    }

    public function testJsonParamWithMultipleRules(): void
    {
        $array = [
            'campo' => 'Bruno123',
        ];
        $rules = [
            'campo' => '{"required":"true","type":"alphaNum","min":"5","max":"20"}',
        ];
        self::assertErrorCount(0, $array, $rules);
    }

    public function testJsonParamWithInvalidValue(): void
    {
        $array = [
            'campo' => 'AB',
        ];
        $rules = [
            'campo' => '{"required":"true","type":"alphaNum","min":"5"}',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testFileNameWithValidFiles(): void
    {
        $fileValid = [
            'name' => 'valid-file-name.pdf',
            'type' => 'application/pdf',
            'tmp_name' => '/tmp/phpTest',
            'error' => 0,
            'size' => 1024,
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
            'testValid' => 'Açúcar',
            'testError' => 'Açúcar doce especial',
        ];
        $rules = [
            'testValid' => 'max:10',
            'testError' => 'max:10',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testMinWithUnicodeChars(): void
    {
        $array = [
            'testValid' => 'Açúcar',
            'testError' => 'Açú',
        ];
        $rules = [
            'testValid' => 'min:5',
            'testError' => 'min:5',
        ];
        self::assertErrorCount(1, $array, $rules);
    }

    public function testEmailWithInvalidFormats(): void
    {
        $array = [
            'testValid' => 'test@example.com',
            'testErrorNoAt' => 'testexample.com',
            'testErrorNoDomain' => 'test@',
            'testErrorNoUser' => '@example.com',
        ];
        $rules = [
            'testValid' => 'email',
            'testErrorNoAt' => 'email',
            'testErrorNoDomain' => 'email',
            'testErrorNoUser' => 'email',
        ];
        self::assertErrorCount(3, $array, $rules);
    }

    public function testPhoneWithDifferentLengths(): void
    {
        $array = [
            'testValid10' => '1133334444',
            'testValid11' => '11999998888',
            'testError9' => '113333444',
            'testError12' => '119999988880',
        ];
        $rules = [
            'testValid10' => 'phone',
            'testValid11' => 'phone',
            'testError9' => 'phone',
            'testError12' => 'phone',
        ];
        self::assertErrorCount(2, $array, $rules);
    }
}
