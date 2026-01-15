<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    public function testCaptureClientIp(): void
    {
        $ip = Utility::captureClientIp();
        self::assertNull($ip); //Phpunit not read global ambient
    }

    public function testGeneratePassword(): void
    {
        $passWordFull = Utility::generatePassword(10);

        self::assertEquals(10, strlen($passWordFull));
        self::assertTrue((bool) preg_match('@[A-Z]@', $passWordFull));
        self::assertTrue((bool) preg_match('@[a-z]@', $passWordFull));
        self::assertTrue((bool) preg_match('@[0-9]@', $passWordFull));
        self::assertTrue((bool) preg_match("/(?=.*[^A-Za-z\\d])/", $passWordFull));
    }

    public function testGeneratePasswordOnlyUppercase(): void
    {
        $password = Utility::generatePassword(8, true, false, false, false);

        self::assertEquals(8, strlen($password));
        self::assertTrue((bool) preg_match('@^[A-Z]+$@', $password));
    }

    public function testGeneratePasswordOnlyLowercase(): void
    {
        $password = Utility::generatePassword(8, false, true, false, false);

        self::assertEquals(8, strlen($password));
        self::assertTrue((bool) preg_match('@^[a-z]+$@', $password));
    }

    public function testGeneratePasswordOnlyNumbers(): void
    {
        $password = Utility::generatePassword(8, false, false, true, false);

        self::assertEquals(8, strlen($password));
        self::assertTrue((bool) preg_match('@^[0-9]+$@', $password));
    }

    public function testGeneratePasswordOnlySymbols(): void
    {
        $password = Utility::generatePassword(8, false, false, false, true);

        self::assertEquals(8, strlen($password));
        self::assertTrue((bool) preg_match('/^[@#$!()\-+%=]+$/', $password));
    }

    public function testGeneratePasswordWithoutSymbols(): void
    {
        $password = Utility::generatePassword(12, true, true, true, false);

        self::assertEquals(12, strlen($password));
        self::assertTrue((bool) preg_match('@[A-Z]@', $password));
        self::assertTrue((bool) preg_match('@[a-z]@', $password));
        self::assertTrue((bool) preg_match('@[0-9]@', $password));
        self::assertFalse((bool) preg_match('/[@#$!()\-+%=]/', $password));
    }

    public function testGeneratePasswordMinimumSize(): void
    {
        $password = Utility::generatePassword(1, true, false, false, false);

        self::assertEquals(1, strlen($password));
    }

    public function testGeneratePasswordLargeSize(): void
    {
        $password = Utility::generatePassword(50);

        self::assertEquals(50, strlen($password));
    }

    public function testGeneratePasswordUppercaseAndNumbers(): void
    {
        $password = Utility::generatePassword(10, true, false, true, false);

        self::assertEquals(10, strlen($password));
        self::assertTrue((bool) preg_match('@^[A-Z0-9]+$@', $password));
    }

    public function testBuildUrl(): void
    {
        self::assertSame(
            'https://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando', 'on'),
            'Erro ao executar a função buildUrl!',
        );
        self::assertSame(
            'http://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando'),
            'Erro ao executar a função testBuildUrl!',
        );
        self::assertNotSame(
            'https://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando'),
            'Erro ao executar a função testBuildUrl!',
        );
        self::assertNotSame(
            'http://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando', 'on'),
            'Erro ao executar a função testBuildUrl!',
        );
        self::assertNotSame(
            'http://localhost/Projeto/teste',
            Utility::buildUrl('localhost', '/Projeto/testando'),
            'Erro ao executar a função testBuildUrl!',
        );
        self::assertNotSame(
            'https://localhost/Projeto/teste',
            Utility::buildUrl('localhost', '/Projeto/testando', 'on'),
            'Erro ao executar a função testBuildUrl!',
        );
    }

    public function testBuildUrlWithEmptyPath(): void
    {
        self::assertSame('http://localhost', Utility::buildUrl('localhost'));
        self::assertSame('https://localhost', Utility::buildUrl('localhost', '', 'on'));
    }

    public function testBuildUrlWithDifferentHosts(): void
    {
        self::assertSame('http://example.com/api', Utility::buildUrl('example.com', '/api'));
        self::assertSame('https://api.example.com/v1', Utility::buildUrl('api.example.com', '/v1', 'on'));
    }

    public function testBuildUrlWithPort(): void
    {
        self::assertSame('http://localhost:8080/api', Utility::buildUrl('localhost:8080', '/api'));
        self::assertSame('https://localhost:443/secure', Utility::buildUrl('localhost:443', '/secure', 'on'));
    }

    public function testBuildUrlWithQueryString(): void
    {
        self::assertSame(
            'http://localhost/api?param=value',
            Utility::buildUrl('localhost', '/api?param=value')
        );
    }

    public function testBuildUrlHttpsWithNullValue(): void
    {
        self::assertSame('http://localhost/path', Utility::buildUrl('localhost', '/path', null));
    }

    public function testBuildUrlHttpsWithEmptyString(): void
    {
        self::assertSame('http://localhost/path', Utility::buildUrl('localhost', '/path', ''));
    }

    public function testBuildUrlHttpsWithOffValue(): void
    {
        self::assertSame('http://localhost/path', Utility::buildUrl('localhost', '/path', 'off'));
    }
}
