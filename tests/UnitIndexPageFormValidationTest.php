<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitIndexPageFormValidationTest extends TestCase
{
    public function testFormValidationSuccess(): void
    {
        $array = [
            'cpfOuCnpj' => '04764334879',
            'nomePais' => 'Brasil',
            'dadosEmpresa' => 'cooper',
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertEmpty($validator->getErros());
    }

    public function testFormValidationCpfCnpjErrors(): void
    {
        $array = [
            'cpfOuCnpj' => '12345678901', // CPF inválido
            'nomePais' => 'Brasil',
            'dadosEmpresa' => 'cooper',
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('cpfOuCnpj', $validator->getErros());
    }

    public function testFormValidationNomePaisErrors(): void
    {
        $array = [
            'cpfOuCnpj' => '04764334879',
            'nomePais' => 'Br', // Muito curto
            'dadosEmpresa' => 'cooper',
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('nomePais', $validator->getErros());
    }

    public function testFormValidationDadosEmpresaErrors(): void
    {
        $array = [
            'cpfOuCnpj' => '04764334879',
            'nomePais' => 'Brasil',
            'dadosEmpresa' => 'ab', // Muito curto
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('dadosEmpresa', $validator->getErros());
    }

    public function testFormValidationAlphaWithNumbers(): void
    {
        $array = [
            'cpfOuCnpj' => '04764334879',
            'nomePais' => 'Brasil123', // Contém números
            'dadosEmpresa' => 'cooper',
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('nomePais', $validator->getErros());
    }

    public function testFormValidationRequiredFields(): void
    {
        $array = [
            'cpfOuCnpj' => '',
            'nomePais' => '',
            'dadosEmpresa' => '',
        ];
        $rules = [
            'cpfOuCnpj' => 'identifierOrCompany',
            'nomePais' => 'required|alpha|min:3|max:30',
            'dadosEmpresa' => 'required|alpha|min:3|max:80',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('nomePais', $validator->getErros());
        self::assertArrayHasKey('dadosEmpresa', $validator->getErros());
    }

    public function testFileUploadSingleSuccess(): void
    {
        $fileUploadSingle = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.jpg',
            'error' => 0,
            'size' => 50000,
        ];
        
        $array = ['fileUploadSingle' => $fileUploadSingle];
        $rules = ['fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:150000|maxFile:1'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertEmpty($validator->getErros());
    }

    public function testFileUploadMultipleSuccess(): void
    {
        $fileUploadMultiple = [
            'name' => ['0' => 'test1.jpeg', '1' => 'test2.png'],
            'type' => ['0' => 'image/jpeg', '1' => 'image/png'],
            'tmp_name' => ['0' => '/tmp/test1.jpeg', '1' => '/tmp/test2.png'],
            'error' => ['0' => 0, '1' => 0],
            'size' => ['0' => 50000, '1' => 60000],
        ];
        
        $array = ['fileUploadMultiple' => $fileUploadMultiple];
        $rules = ['fileUploadMultiple' => 'fileName|mimeType:jpeg;png|minFile:1|maxFile:3|minUploadSize:10|maxUploadSize:150000'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertEmpty($validator->getErros());
    }

    public function testFileUploadMimeTypeError(): void
    {
        $fileUploadSingle = [
            'name' => 'test.html',
            'type' => 'text/html',
            'tmp_name' => '/tmp/test.html',
            'error' => 0,
            'size' => 50000,
        ];
        
        $array = ['fileUploadSingle' => $fileUploadSingle];
        $rules = ['fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:150000|maxFile:1'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('fileUploadSingle', $validator->getErros());
    }

    public function testFileUploadSizeError(): void
    {
        $fileUploadSingle = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.jpg',
            'error' => 0,
            'size' => 200000, // Maior que 150000
        ];
        
        $array = ['fileUploadSingle' => $fileUploadSingle];
        $rules = ['fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:150000|maxFile:1'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('fileUploadSingle', $validator->getErros());
    }

    public function testFileUploadRequiredError(): void
    {
        $fileUploadSingle = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => 4, // UPLOAD_ERR_NO_FILE
            'size' => 0,
        ];
        
        $array = ['fileUploadSingle' => $fileUploadSingle];
        $rules = ['fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:150000|maxFile:1'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('fileUploadSingle', $validator->getErros());
    }

    public function testFileUploadMaxFileError(): void
    {
        $fileUploadMultiple = [
            'name' => ['0' => 'test1.jpeg', '1' => 'test2.png', '2' => 'test3.jpeg', '3' => 'test4.png'], // 4 arquivos
            'type' => ['0' => 'image/jpeg', '1' => 'image/png', '2' => 'image/jpeg', '3' => 'image/png'],
            'tmp_name' => ['0' => '/tmp/test1.jpeg', '1' => '/tmp/test2.png', '2' => '/tmp/test3.jpeg', '3' => '/tmp/test4.png'],
            'error' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0],
            'size' => ['0' => 50000, '1' => 60000, '2' => 70000, '3' => 80000],
        ];
        
        $array = ['fileUploadMultiple' => $fileUploadMultiple];
        $rules = ['fileUploadMultiple' => 'fileName|mimeType:jpeg;png|minFile:1|maxFile:3|minUploadSize:10|maxUploadSize:150000'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('fileUploadMultiple', $validator->getErros());
    }

    public function testCpfValidation(): void
    {
        $array = ['cpf' => '04764334879'];
        $rules = ['cpf' => 'identifierOrCompany'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertEmpty($validator->getErros());
    }

    public function testCnpjValidation(): void
    {
        $array = ['cnpj' => '39.678.379/0001-29'];
        $rules = ['cnpj' => 'identifierOrCompany'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertEmpty($validator->getErros());
    }

    public function testInvalidCpfValidation(): void
    {
        $array = ['cpf' => '12345678901'];
        $rules = ['cpf' => 'identifierOrCompany'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('cpf', $validator->getErros());
    }

    public function testInvalidCnpjValidation(): void
    {
        $array = ['cnpj' => '12.345.678/0001-90'];
        $rules = ['cnpj' => 'identifierOrCompany'];
        
        $validator = new Validator();
        $validator->set($array, $rules);
        
        self::assertNotEmpty($validator->getErros());
        self::assertArrayHasKey('cnpj', $validator->getErros());
    }
} 