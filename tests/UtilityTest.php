<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Utility;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    /**
     * @var array<mixed>
     */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testCaptureClientIpReturnsNullWhenNoServerKeyIsPresent(): void
    {
        unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']);

        self::assertNull(Utility::captureClientIp());
    }

    public function testCaptureClientIpPrefersClientIpHeader(): void
    {
        $_SERVER['HTTP_CLIENT_IP'] = '10.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.2';
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';

        self::assertSame('10.0.0.1', Utility::captureClientIp());
    }

    public function testCaptureClientIpFallsBackToForwardedFor(): void
    {
        unset($_SERVER['HTTP_CLIENT_IP']);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.2';
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';

        self::assertSame('10.0.0.2', Utility::captureClientIp());
    }

    public function testCaptureClientIpFallsBackToRemoteAddr(): void
    {
        unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';

        self::assertSame('10.0.0.3', Utility::captureClientIp());
    }

    public function testCaptureClientIpIgnoresEmptyAndNonStringValues(): void
    {
        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = ['10.0.0.9'];
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';

        self::assertSame('10.0.0.3', Utility::captureClientIp());
    }

    /**
     * @return array<string, array{0: int, 1: bool, 2: bool, 3: bool, 4: bool, 5: string}>
     */
    public static function passwordCharsetProvider(): array
    {
        return [
            'todos os conjuntos' => [20, true, true, true, true, '/^[A-Za-z0-9@#$!()\-+%=]+$/'],
            'somente maiusculas' => [8, true, false, false, false, '/^[A-Z]+$/'],
            'somente minusculas' => [8, false, true, false, false, '/^[a-z]+$/'],
            'somente numeros' => [8, false, false, true, false, '/^[0-9]+$/'],
            'somente simbolos' => [8, false, false, false, true, '/^[@#$!()\-+%=]+$/'],
            'sem simbolos' => [12, true, true, true, false, '/^[A-Za-z0-9]+$/'],
            'maiusculas e numeros' => [10, true, false, true, false, '/^[A-Z0-9]+$/'],
            'tamanho minimo de um grupo' => [1, true, false, false, false, '/^[A-Z]$/'],
        ];
    }

    #[DataProvider('passwordCharsetProvider')]
    public function testGeneratePasswordRespectsCharset(
        int $size,
        bool $uppercase,
        bool $lowercase,
        bool $numbers,
        bool $symbols,
        string $pattern,
    ): void {
        $password = Utility::generatePassword($size, $uppercase, $lowercase, $numbers, $symbols);

        self::assertSame($size, strlen($password));
        self::assertMatchesRegularExpression($pattern, $password);
    }

    public function testGeneratePasswordContainsEveryEnabledGroup(): void
    {
        $password = Utility::generatePassword(10);

        self::assertSame(10, strlen($password));
        self::assertMatchesRegularExpression('/[A-Z]/', $password);
        self::assertMatchesRegularExpression('/[a-z]/', $password);
        self::assertMatchesRegularExpression('/[0-9]/', $password);
        self::assertMatchesRegularExpression('/[^A-Za-z0-9]/', $password);
    }

    /**
     * @return array<string, array{0: int, 1: bool, 2: bool, 3: bool, 4: bool}>
     */
    public static function passwordLongerThanCharsetProvider(): array
    {
        return [
            'maiusculas acima do charset' => [50, true, false, false, false],
            'numeros acima do charset' => [30, false, false, true, false],
            'simbolos acima do charset' => [20, false, false, false, true],
            'todos acima do charset' => [100, true, true, true, true],
        ];
    }

    #[DataProvider('passwordLongerThanCharsetProvider')]
    public function testGeneratePasswordHonoursSizeLargerThanCharset(
        int $size,
        bool $uppercase,
        bool $lowercase,
        bool $numbers,
        bool $symbols,
    ): void {
        $password = Utility::generatePassword($size, $uppercase, $lowercase, $numbers, $symbols);

        self::assertSame($size, strlen($password));
    }

    public function testGeneratePasswordAllowsRepeatedCharacters(): void
    {
        $repeated = false;

        for ($attempt = 0; $attempt < 50 && !$repeated; $attempt++) {
            $password = Utility::generatePassword(30, false, false, true, false);
            $repeated = count(array_unique(str_split($password))) < strlen($password);
        }

        self::assertTrue($repeated, 'Senha de 30 dígitos sobre 10 símbolos precisa repetir caracteres.');
    }

    public function testGeneratePasswordProducesDifferentResults(): void
    {
        $passwords = [];
        for ($i = 0; $i < 20; $i++) {
            $passwords[] = Utility::generatePassword(16);
        }

        self::assertGreaterThan(1, count(array_unique($passwords)));
    }

    public function testGeneratePasswordWithoutAnyCharsetThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ao menos um conjunto');
        Utility::generatePassword(10, false, false, false, false);
    }

    /**
     * @return array<string, array{0: int}>
     */
    public static function invalidPasswordSizeProvider(): array
    {
        return [
            'zero' => [0],
            'negativo' => [-1],
            'menor que os quatro grupos' => [3],
        ];
    }

    #[DataProvider('invalidPasswordSizeProvider')]
    public function testGeneratePasswordWithSizeSmallerThanEnabledGroupsThrowsException(int $size): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tamanho da senha deve ser no mínimo');
        Utility::generatePassword($size);
    }

    public function testGeneratePasswordSizeEqualToNumberOfGroups(): void
    {
        $password = Utility::generatePassword(4);

        self::assertSame(4, strlen($password));
        self::assertMatchesRegularExpression('/[A-Z]/', $password);
        self::assertMatchesRegularExpression('/[a-z]/', $password);
        self::assertMatchesRegularExpression('/[0-9]/', $password);
        self::assertMatchesRegularExpression('/[^A-Za-z0-9]/', $password);
    }

    /**
     * @return array<string, array{0: string|null, 1: string}>
     */
    public static function protocolProvider(): array
    {
        return [
            'on minusculo' => ['on', 'https'],
            'On capitalizado' => ['On', 'https'],
            'ON maiusculo' => ['ON', 'https'],
            'um' => ['1', 'https'],
            'true' => ['true', 'https'],
            'yes' => ['yes', 'https'],
            'off' => ['off', 'http'],
            'zero' => ['0', 'http'],
            'string vazia' => ['', 'http'],
            'nulo' => [null, 'http'],
            'valor desconhecido' => ['banana', 'http'],
        ];
    }

    #[DataProvider('protocolProvider')]
    public function testBuildUrlProtocol(?string $https, string $expectedProtocol): void
    {
        self::assertSame(
            $expectedProtocol . '://localhost/path',
            Utility::buildUrl('localhost', '/path', $https),
        );
    }

    public function testBuildUrl(): void
    {
        self::assertSame(
            'https://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando', 'on'),
        );
        self::assertSame(
            'http://localhost/Projeto/testando',
            Utility::buildUrl('localhost', '/Projeto/testando'),
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
            Utility::buildUrl('localhost', '/api?param=value'),
        );
    }
}
