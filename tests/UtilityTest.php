<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Utility;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    private const IP_CLIENT = '203.0.113.1';
    private const IP_FORWARDED = '203.0.113.2';
    private const IP_REMOTE = '203.0.113.3';

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
        $_SERVER['HTTP_CLIENT_IP'] = self::IP_CLIENT;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = self::IP_FORWARDED;
        $_SERVER['REMOTE_ADDR'] = self::IP_REMOTE;

        self::assertSame(self::IP_CLIENT, Utility::captureClientIp());
    }

    public function testCaptureClientIpFallsBackToForwardedFor(): void
    {
        unset($_SERVER['HTTP_CLIENT_IP']);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = self::IP_FORWARDED;
        $_SERVER['REMOTE_ADDR'] = self::IP_REMOTE;

        self::assertSame(self::IP_FORWARDED, Utility::captureClientIp());
    }

    public function testCaptureClientIpFallsBackToRemoteAddr(): void
    {
        unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = self::IP_REMOTE;

        self::assertSame(self::IP_REMOTE, Utility::captureClientIp());
    }

    public function testCaptureClientIpIgnoresEmptyAndNonStringValues(): void
    {
        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = ['203.0.113.9'];
        $_SERVER['REMOTE_ADDR'] = self::IP_REMOTE;

        self::assertSame(self::IP_REMOTE, Utility::captureClientIp());
    }

    /**
     * @return array<string, array{0: int, 1: bool, 2: bool, 3: bool, 4: bool, 5: string}>
     */
    public static function passwordCharsetProvider(): array
    {
        return [
            'uppercase and numbers' => [10, true, false, true, false, '/^[A-Z0-9]+$/'],
            'without symbols' => [12, true, true, true, false, '/^[A-Za-z0-9]+$/'],
            'uppercase only' => [8, true, false, false, false, '/^[A-Z]+$/'],
            'lowercase only' => [8, false, true, false, false, '/^[a-z]+$/'],
            'numbers only' => [8, false, false, true, false, '/^[0-9]+$/'],
            'symbols only' => [8, false, false, false, true, '/^[@#$!()\-+%=]+$/'],
            'minimum size of one group' => [1, true, false, false, false, '/^[A-Z]$/'],
            'all charsets' => [20, true, true, true, true, '/^[A-Za-z0-9@#$!()\-+%=]+$/'],
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
            'uppercase above charset' => [50, true, false, false, false],
            'numbers above charset' => [30, false, false, true, false],
            'symbols above charset' => [20, false, false, false, true],
            'all above charset' => [100, true, true, true, true],
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
            'smaller than the four groups' => [3],
            'negative' => [-1],
            'zero' => [0],
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
            'null value' => [null, 'http'],
            'off' => ['off', 'http'],
            'capitalized on' => ['On', 'https'],
            'uppercase on' => ['ON', 'https'],
            'lowercase on' => ['on', 'https'],
            'empty string' => ['', 'http'],
            'true' => ['true', 'https'],
            'one' => ['1', 'https'],
            'unknown value' => ['banana', 'http'],
            'yes' => ['yes', 'https'],
            'zero' => ['0', 'http'],
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
