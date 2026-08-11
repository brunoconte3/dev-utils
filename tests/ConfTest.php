<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\conf\Conf;
use PHPUnit\Framework\TestCase;

class ConfTest extends TestCase
{
    private mixed $httpHost = null;
    private mixed $requestUri = null;

    protected function setUp(): void
    {
        $this->httpHost = $_SERVER['HTTP_HOST'] ?? null;
        $this->requestUri = $_SERVER['REQUEST_URI'] ?? null;
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);

        if ($this->httpHost !== null) {
            $_SERVER['HTTP_HOST'] = $this->httpHost;
        }
        if ($this->requestUri !== null) {
            $_SERVER['REQUEST_URI'] = $this->requestUri;
        }
    }

    public function testHostReturnsHttpHostWithPort(): void
    {
        $_SERVER['HTTP_HOST'] = 'dev-utils.local:8080';

        self::assertSame('dev-utils.local:8080', Conf::host());
    }

    public function testHostRemovesCharactersInvalidInUrl(): void
    {
        $_SERVER['HTTP_HOST'] = "dev utils\n.local";

        self::assertSame('devutils.local', Conf::host());
    }

    public function testHostReturnsEmptyStringWhenHeaderIsAbsent(): void
    {
        unset($_SERVER['HTTP_HOST']);

        self::assertSame('', Conf::host());
    }

    public function testHostReturnsEmptyStringWhenHeaderIsNotAString(): void
    {
        $_SERVER['HTTP_HOST'] = ['dev-utils.local'];

        self::assertSame('', Conf::host());
    }

    public function testRequestUriKeepsPathAndQueryString(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/users?page=1&limit=10';

        self::assertSame('/api/users?page=1&limit=10', Conf::requestUri());
    }

    public function testRequestUriReturnsEmptyStringWhenAbsent(): void
    {
        unset($_SERVER['REQUEST_URI']);

        self::assertSame('', Conf::requestUri());
    }

    public function testRequestUriReturnsEmptyStringWhenNotAString(): void
    {
        $_SERVER['REQUEST_URI'] = 10;

        self::assertSame('', Conf::requestUri());
    }

    public function testPathProjectPointsToRepositoryRoot(): void
    {
        self::assertSame(dirname(__DIR__), Conf::pathProject());
        self::assertDirectoryExists(Conf::pathProject() . DIRECTORY_SEPARATOR . 'conf');
    }

    public function testConstructorDefinesGlobalConstants(): void
    {
        $_SERVER['HTTP_HOST'] = 'dev-utils.local';
        $_SERVER['REQUEST_URI'] = '/status';

        $conf = new Conf();

        self::assertInstanceOf(Conf::class, $conf);
        self::assertSame('dev-utils.local', URL_HOST);
        self::assertSame('dev-utils.local/status', URL);
        self::assertSame(dirname(__DIR__), PATH_PROJECT);
    }

    public function testConstructorDoesNotRedefineExistingConstants(): void
    {
        $_SERVER['HTTP_HOST'] = 'dev-utils.local';
        $_SERVER['REQUEST_URI'] = '/status';
        $first = new Conf();

        $_SERVER['HTTP_HOST'] = 'outro-host.local';
        $_SERVER['REQUEST_URI'] = '/outro';
        $second = new Conf();

        self::assertNotSame($first, $second);
        self::assertSame('dev-utils.local', URL_HOST);
        self::assertSame('dev-utils.local/status', URL);
    }
}
