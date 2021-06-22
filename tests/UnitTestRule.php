<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestRule extends TestCase
{
    private function mountFileSingle(): array
    {
        $l = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__)) . '/../public_html' . $l . 'static' . $l . 'img' . $l;

        return [
            'name'     => 'fileUpload ',
            'type'     => 'image/jpeg',
            'tmp_name' => $path . 'iconTest.png',
            'error'    => 0,
            'size'     => 19639,
        ];
    }

    public function testArray(): void
    {
        $array = ['testError' => 'a', 'testValid' => ['a' => 1, 'b' => 2]];
        $rules = ['testError' => 'array', 'testValid' => 'array'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testArrayValues(): void
    {
        $array = ['testError' => 'M', 'testValid' => 'S'];
        $rules = ['testError' => 'arrayValues:S-N-T', 'testValid' => 'arrayValues:S-N-T'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testBool(): void
    {
        $array = ['testError' => 'a123', 'testValid' => true];
        $rules = ['testError' => 'int', 'testValid' => 'bool'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testCompanyIdentification(): void
    {
        $array = [
            'testError' => '52186923000120',
            'testErrorEmpty' => '',
            'testValid' => '21111527000163',
            'testExceptionError' => '12123456000712',
            'testExceptionValid' => '00000000000000'
        ];

        $rules = [
            'testError' => 'companyIdentification',
            'testErrorEmpty' => 'companyIdentification',
            'testValid' => 'companyIdentification',
            'testExceptionError' => 'companyIdentification:12123456000712',
            'testExceptionValid' => 'companyIdentification:00000000000000;22222222222222'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(3, $validator->getErros());
    }

    public function testDateAmerican(): void
    {
        $array = ['testError' => '1990-04-31', 'testErrorEmpty' => '', 'testValid' => '1990-04-30'];
        $rules = ['testError' => 'dateAmerican', 'testErrorEmpty' => 'dateAmerican', 'testValid' => 'dateAmerican'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testDateBrazil(): void
    {
        $array = ['testError' => '31042020', 'testErrorEmpty' => '', 'testValid' => '31052020'];
        $rules = ['testError' => 'dateBrazil', 'testErrorEmpty' => 'dateBrazil', 'testValid' => 'dateBrazil'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testEmail(): void
    {
        $array = ['testError' => 'bruno.com', 'testValid' => 'brunoconte3@gmail.com'];
        $rules = ['testError' => 'email', 'testValid' => 'email'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testIdentifier(): void
    {
        $array = ['testError' => '06669987788', 'testValid' => '55634405831'];
        $rules = ['testError' => 'identifier', 'testValid' => 'identifier'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testInt(): void
    {
        $array = ['testError' => 'a123', 'testValid' => 123];
        $rules = ['testError' => 'int', 'testValid' => 'int'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testIp(): void
    {
        $array = ['testError' => '1.1.0', 'testValid' => '10.202.0.58'];
        $rules = ['testError' => 'ip', 'testValid' => 'ip'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testFloat(): void
    {
        $array = ['testError' => 'a1', 'testValid' => '10.125'];
        $rules = ['testError' => 'float', 'testValid' => 'float'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testHour(): void
    {
        $array = ['testError' => '24:03', 'testValid' => '21:03'];
        $rules = ['testError' => '{"type":"hour"}', 'testValid' => '{"type":"hour"}'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testLower(): void
    {
        $array = ['testError' => 'Abcdção', 'testValid' => 'abcdção'];
        $rules = ['testError' => '{"type":"lower"}', 'testValid' => '{"type":"lower"}'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMac(): void
    {
        $array = ['testError' => '00:00', 'testValid' => '00-D0-56-F2-B5-12'];
        $rules = ['testError' => 'mac', 'testValid' => 'mac'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMax(): void
    {
        $array = ['testError' => 123, 'testValid' => "Avenida Pedra D'Água"];
        $rules = ['testError' => 'max:2', 'testValid' => 'max:20'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMaxWords(): void
    {
        $array = ['testError' => 'Jorge da Silva', 'testValid' => 'Bruno Conte'];
        $rules = ['testError' => 'maxWords:2', 'testValid' => 'maxWords:2'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMin(): void
    {
        $array = ['testError' => '123', 'testValid' => "Avenida Pedra D'Água"];
        $rules = ['testError' => 'min:5', 'testValid' => 'min:20'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMinWords(): void
    {
        $array = ['testError' => 'Jorge da Silva', 'testValid' => 'Bruno Conte'];
        $rules = ['testError' => 'minWords:4', 'testValid' => 'minWords:2'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNoWeekend(): void
    {
        $array = ['testError' => '10/10/2020', 'testValid' => '16/10/2020'];
        $rules = ['testError' => 'noWeekend', 'testValid' => 'noWeekend'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumeric(): void
    {
        $array = ['testError' => 'a', 'testValid' => 123];
        $rules = ['testError' => 'numeric', 'testValid' => 'numeric'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testNumMax(): void
    {
        $array = ['testError' => 32, 'testValid' => 31, 'testErrorMaxZero' => '2'];
        $rules = ['testError' => 'numMax:31', 'testValid' => 'numMax:31', 'testErrorMaxZero' => 'numMax:0'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testNumMin(): void
    {
        $array = ['testError' => 2, 'testeErrorNoInt' => 'a', 'testValid' => 8];
        $rules = ['testError' => 'numMin:5', 'testeErrorNoInt' => 'numMin:5', 'testValid' => 'numMin:5'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testOptional(): void
    {
        $validator = new Validator();
        $validator->set(['test' => null], ['test' => 'optional|min:2|int']);
        self::assertFalse($validator->getErros());
    }

    public function testParamJson(): void
    {
        $array = [
            'testError' => '@&451',
            'testValid' => 123
        ];
        $rules = [
            'testError' => '{"required":"true","type":"alpha"}',
            'testValid' => '{"required":"true","type":"int"}'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testPhone(): void
    {
        $array = ['testError' => '444569874', 'testValid' => '4433467847', 'testMask' => '(44) 99932-5847'];
        $rules = ['testError' => 'phone', 'testValid' => 'phone', 'testMask' => 'phone'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testPlate(): void
    {
        $array = ['testError' => 'aXI3668', 'testValid' => 'AXI-3668'];
        $rules = ['testError' => 'plate', 'testValid' => 'plate'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testRegEx(): void
    {
        $array = ['testError' => 'bruno_conte3', 'testValid' => 'Bruno Conte'];
        $rules = ['testError' => 'regex:/^[a-zA-Z\s]+$/', 'testValid' => 'regex:/^[a-zA-Z\s]+$/'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testRequired(): void
    {
        $array = [
            'a' => '',
            'b' => null,
            'c' => false,
            'd' => [],
            'e' => '   ',
            'f' => 'abc',
            'g' => 123,
            'h' => '0',
            'i' => 0
        ];
        $rules = [
            'a' => 'required',
            'b' => 'required',
            'c' => 'required',
            'd' => 'required',
            'e' => 'required',
            'f' => 'required',
            'g' => 'required',
            'h' => 'required',
            'i' => 'required'
        ];

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
            'testAlphaNumNoSpecialValid' => 'Ele usa um dicionario com mais de 200 palavras'
        ];
        $rules = [
            'testAlphaError'             => 'type:alpha',
            'testAlphaNoSpecialError'    => 'type:alphaNoSpecial',
            'testAlphaNumError'          => 'type:alphaNum',
            'testAlphaNumNoSpecialError' => 'type:alphaNumNoSpecial',
            'testAlphaValid'             => 'type:alpha',
            'testAlphaNoSpecialValid'    => 'type:alphaNoSpecial',
            'testAlphaNumValid'          => 'type:alphaNum',
            'testAlphaNumNoSpecialValid' => 'type:alphaNumNoSpecial'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(4, $validator->getErros());
    }

    public function testUpper(): void
    {
        $array = ['testError' => 'AbcDçÃo', 'testValid' => 'ABCDÇÃO'];
        $rules = ['testError' => 'upper', 'testValid' => 'upper'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testUrl(): void
    {
        $array = ['testError' => 'ww.test.c', 'testValid' => 'https://www.google.com.br'];
        $rules = ['testError' => 'url', 'testValid' => 'url'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testZipcode(): void
    {
        $array = ['testError' => '870475', 'testValid' => '87047510'];
        $rules = ['testError' => 'zipcode', 'testValid' => 'zipcode'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testCustomMessage(): void
    {
        $msg = 'Mensagem customizada aqui, devendo conter no mínimo uma vírgula!';
        $array = [
            'textoError' => 'abc',
            'textoValid' => 'abcde'
        ];
        $rules = [
            'textoError' => 'required|min:5, ' . $msg . '|max:20',
            'textoValid' => 'required|min:5, ' . $msg . '|max:20'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(1, $validator->getErros());
        self::assertEquals($msg, $validator->getErros()['textoError']);
    }

    public function testNotSpace(): void
    {
        $array = ['validarEspacoError' => 'BRU C', 'validarEspacoValid' => 'BRUC'];
        $rules = ['validarEspacoError' => 'notSpace', 'validarEspacoValid' => 'notSpace'];

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(1, $validator->getErros());
    }

    public function testJson(): void
    {
        $array = ['validaJsonError' => '"nome": "Bruno"}', 'validaJsonValid' => '{"nome": "Bruno"}'];
        $rules = ['validaJsonError' => 'type:json', 'validaJsonValid' => 'type:json'];

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(1, $validator->getErros());
    }

    public function testNumMonth(): void
    {
        $array = ['validaMesError' => 13, 'validaMesValid' => 10];
        $rules = ['validaMesError' => 'numMonth', 'validaMesValid' => 'numMonth'];

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(1, $validator->getErros());
    }

    public function testIdentifierOrCompany(): void
    {
        $array = [
            'cpfOuCnpjerror' => '96.284.092.0001/59',
            'cpfOuCnpjValid' => '96.284.092/0001-58',
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

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(3, $validator->getErros());
    }

    public function testFileMaxUploadSize(): void
    {
        $fileUploadSingle = [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 8488,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 8488, '1' => 818465],
        ];

        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'maxUploadSize:5550',
            'fileUploadMultiple' => 'maxUploadSize:5550',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testFileMinUploadSize(): void
    {
        $fileUploadSingle = [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 3589,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 4450, '1' => 4980],
        ];

        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'minUploadSize:5550',
            'fileUploadMultiple' => 'minUploadSize:5550',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testFileMimeType(): void
    {
        $fileUploadSingle = [
            'name' => 'JPG - Validação upload v.1.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpODnLGo',
            'error' => 0,
            'size' => 8488,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 8488, '1' => 818465],
        ];

        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'mimeType:jpeg;png',
            'fileUploadMultiple' => 'mimeType:png;svg',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
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
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 8488, '1' => 818465],
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
        self::assertCount(2, $validator->getErros());
    }

    public function testRequiredFile(): void
    {
        $fileUploadSingle = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => 4,
            'size' => 0,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => ''],
            'type'     => ['0' => ''],
            'tmp_name' => ['0' => ''],
            'error'    => ['0' => 4],
            'size'     => ['0' => 0],
        ];

        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'requiredFile',
            'fileUploadMultiple' => 'requiredFile',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }

    public function testMaxFile(): void
    {
        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg', '1' => 'PDF - Validação upload v.1.pdf'],
            'type'     => ['0' => 'image/jpeg', '1' => 'application/pdf'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo', '1' => '/tmp/phpfmb0tL'],
            'error'    => ['0' => 0, '1' => 0],
            'size'     => ['0' => 8488, '1' => 818465],
        ];

        $array = ['fileUploadMultiple' => $fileUploadMultiple];
        $rules = ['fileUploadMultiple' => 'maxFile:1'];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testMinFile(): void
    {
        $fileUploadSingle = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => 4,
            'size' => 0,
        ];

        $fileUploadMultiple = [
            'name'     => ['0' => 'JPG - Validação upload v.1.jpg'],
            'type'     => ['0' => 'image/jpeg'],
            'tmp_name' => ['0' => '/tmp/phpODnLGo'],
            'error'    => ['0' => 0],
            'size'     => ['0' => 8488],
        ];

        $array = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'minFile:1',
            'fileUploadMultiple' => 'minFile:2',
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
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

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(2, $validator->getErros());
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

        $validator = new Validator();
        $validator->set($array, $rules);

        self::assertCount(2, $validator->getErros());
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

        self::assertCount(1, $validator->getErros());
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

        self::assertCount(1, $validator->getErros());
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

        self::assertCount(1, $validator->getErros());
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

        self::assertCount(1, $validator->getErros());
    }
}
