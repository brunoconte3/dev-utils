<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Uuid::class)]
final class UuidTest extends TestCase
{
    private const UUID_V1 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    private const UUID_V4 = '550e8400-e29b-41d4-a716-446655440000';
    private const UUID_V7 = '01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a';
    private const LAYOUT_V7_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
    private const VARIANT_NIBBLE_POSITION = 19;

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function validUuidProvider(): array
    {
        return [
            'v1' => [self::UUID_V1, 1],
            'v2' => ['000003e8-cbb4-21ed-b200-325096b39f47', 2],
            'v3' => ['a3bb189e-8bf9-3888-9912-ace4e6543002', 3],
            'v4' => [self::UUID_V4, 4],
            'v5' => ['74738ff5-5367-5958-9aee-98fffdcd1876', 5],
            'v6' => ['1ef0c0d5-cf31-6f45-86a9-1e2b72a3e1ef', 6],
            'v7' => [self::UUID_V7, 7],
            'v8' => ['320c3d4d-cc00-875b-8ec9-32d5f69181c0', 8],
            'v4 variant 8' => ['550e8400-e29b-41d4-8716-446655440000', 4],
            'v4 variant 9' => ['550e8400-e29b-41d4-9716-446655440000', 4],
            'v4 variant b' => ['550e8400-e29b-41d4-b716-446655440000', 4],
            'v4 mixed case' => ['550E8400-e29b-41D4-A716-446655440000', 4],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function malformedUuidProvider(): array
    {
        return [
            'all placeholders' => ['ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ'],
            'braces' => ['{550e8400-e29b-41d4-a716-446655440000}'],
            'empty' => [''],
            'extra group' => ['550e8400-e29b-41d4-a716-446655440000-extra'],
            'hyphens only' => ['------------------------------------'],
            'inner space' => ['550e8400-e29b -41d4-a716-446655440000'],
            'leading line feed' => ["\n550e8400-e29b-41d4-a716-446655440000"],
            'leading space' => [' 550e8400-e29b-41d4-a716-446655440000'],
            'missing hyphens' => ['550e8400e29b41d4a716446655440000'],
            'non hex char' => ['550e8400-e29b-41d4-a716-44665544000g'],
            'special char' => ['550e8400-e29b-41d4-a716-44665544000!'],
            'surrounded by spaces' => [' 550e8400-e29b-41d4-a716-446655440000 '],
            'too long' => ['550e8400-e29b-41d4-a716-446655440000a'],
            'too short' => ['550e8400-e29b-41d4-a716-4466554400'],
            'trailing carriage return' => ["550e8400-e29b-41d4-a716-446655440000\r"],
            'trailing crlf' => ["550e8400-e29b-41d4-a716-446655440000\r\n"],
            'trailing hyphen' => ['550e8400-e29b-41d4-a716-446655440000-'],
            'trailing line feed' => ["550e8400-e29b-41d4-a716-446655440000\n"],
            'trailing null byte' => ["550e8400-e29b-41d4-a716-446655440000\0"],
            'trailing space' => ['550e8400-e29b-41d4-a716-446655440000 '],
            'trailing tab' => ["550e8400-e29b-41d4-a716-446655440000\t"],
            'truncated' => ['550e8400-e29b-41d4-a716'],
            'urn prefix' => ['urn:uuid:550e8400-e29b-41d4-a716-446655440000'],
            'wrong hyphen position' => ['550e84-00e29b-41d4-a716-446655440000'],
            'wrong hyphen position on third group' => ['550e8400-e29b41d4-a716-446655440000'],
            'words' => ['not-a-uuid'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unknownVersionProvider(): array
    {
        return [
            'max uuid' => ['ffffffff-ffff-ffff-ffff-ffffffffffff'],
            'nil uuid' => ['00000000-0000-0000-0000-000000000000'],
            'version 0' => ['550e8400-e29b-01d4-a716-446655440000'],
            'version 9' => ['550e8400-e29b-91d4-a716-446655440000'],
            'version a' => ['550e8400-e29b-a1d4-a716-446655440000'],
            'version b' => ['550e8400-e29b-b1d4-a716-446655440000'],
            'version c' => ['550e8400-e29b-c1d4-a716-446655440000'],
            'version d' => ['550e8400-e29b-d1d4-a716-446655440000'],
            'version e' => ['550e8400-e29b-e1d4-a716-446655440000'],
            'version f' => ['550e8400-e29b-f1d4-a716-446655440000'],
            'version f uppercase' => ['550e8400-e29b-F1d4-a716-446655440000'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidVariantProvider(): array
    {
        return [
            'variant 0' => ['550e8400-e29b-41d4-0716-446655440000'],
            'variant 7' => ['550e8400-e29b-41d4-7716-446655440000'],
            'variant c' => ['550e8400-e29b-41d4-c716-446655440000'],
            'variant c uppercase' => ['550e8400-e29b-41d4-C716-446655440000'],
            'variant d' => ['550e8400-e29b-41d4-d716-446655440000'],
            'variant e' => ['550e8400-e29b-41d4-e716-446655440000'],
            'variant f' => ['550e8400-e29b-41d4-f716-446655440000'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function versionMismatchProvider(): array
    {
        return [
            'v1 asked as v5' => [self::UUID_V1, 5],
            'v4 asked as v7' => [self::UUID_V4, 7],
            'v7 asked as v4' => [self::UUID_V7, 4],
            'version above range' => [self::UUID_V4, 9],
            'version below range' => [self::UUID_V4, 0],
            'version negative' => [self::UUID_V4, -1],
            'version out of nibble' => [self::UUID_V4, 99],
        ];
    }

    public function testGenerateProducesRfcCompliantVersion7(): void
    {
        $uuid = Uuid::generate();

        self::assertMatchesRegularExpression(self::LAYOUT_V7_REGEX, $uuid);
        self::assertTrue(Uuid::isValid($uuid));
        self::assertTrue(Uuid::isValid($uuid, 7));
        self::assertFalse(Uuid::isValid($uuid, 4));
    }

    public function testGenerateProducesGroupsWithCanonicalLengths(): void
    {
        $parts = explode('-', Uuid::generate());

        self::assertCount(5, $parts);
        self::assertSame([8, 4, 4, 4, 12], array_map('strlen', $parts));
    }

    public function testGenerateEncodesCurrentTimestampInMilliseconds(): void
    {
        $before = (int) (microtime(true) * 1000);
        $uuid = Uuid::generate();
        $after = (int) (microtime(true) * 1000);

        $timestamp = (int) hexdec(substr($uuid, 0, 8) . substr($uuid, 9, 4));

        self::assertGreaterThanOrEqual($before, $timestamp);
        self::assertLessThanOrEqual($after, $timestamp);
    }

    public function testGenerateIsSortableByCreationOrder(): void
    {
        $uuids = [];
        for ($i = 0; $i < 5; $i++) {
            $uuids[] = Uuid::generate();
            usleep(1100);
        }

        $sorted = $uuids;
        sort($sorted, SORT_STRING);

        self::assertSame($sorted, $uuids);
    }

    public function testGenerateIsUnique(): void
    {
        $uuids = [];
        for ($i = 0; $i < 1000; $i++) {
            $uuids[] = Uuid::generate();
        }

        self::assertCount(1000, array_unique($uuids));
    }

    public function testGenerateIsValidWhenUppercased(): void
    {
        $uuid = strtoupper(Uuid::generate());

        self::assertTrue(Uuid::isValid($uuid));
        self::assertTrue(Uuid::isValid($uuid, 7));
    }

    #[DataProvider('validUuidProvider')]
    public function testIsValidAcceptsEveryRfcVersion(string $uuid, int $version): void
    {
        self::assertTrue(Uuid::isValid($uuid));
        self::assertTrue(Uuid::isValid($uuid, null));
        self::assertTrue(Uuid::isValid($uuid, $version));
        self::assertTrue(Uuid::isValid(strtoupper($uuid), $version));
    }

    #[DataProvider('malformedUuidProvider')]
    public function testIsValidRejectsMalformedInput(string $uuid): void
    {
        self::assertFalse(Uuid::isValid($uuid));
        self::assertFalse(Uuid::isValid($uuid, 4));
    }

    #[DataProvider('unknownVersionProvider')]
    public function testIsValidRejectsUnknownVersion(string $uuid): void
    {
        self::assertFalse(Uuid::isValid($uuid));
    }

    #[DataProvider('invalidVariantProvider')]
    public function testIsValidRejectsNonRfcVariant(string $uuid): void
    {
        self::assertFalse(Uuid::isValid($uuid));
        self::assertFalse(Uuid::isValid($uuid, 4));

        $withRfcVariant = substr_replace($uuid, '8', self::VARIANT_NIBBLE_POSITION, 1);

        self::assertTrue(Uuid::isValid($withRfcVariant, 4));
    }

    #[DataProvider('versionMismatchProvider')]
    public function testIsValidRejectsVersionMismatch(string $uuid, int $version): void
    {
        self::assertFalse(Uuid::isValid($uuid, $version));
    }
}
