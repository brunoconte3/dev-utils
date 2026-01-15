<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testGenerate(): void
    {
        $uuid = Uuid::generate();

        self::assertTrue(Uuid::isValid($uuid));
        self::assertTrue(Uuid::isValid($uuid, 7));
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    public function testGenerateIsOrderable(): void
    {
        $uuid1 = Uuid::generate();
        usleep(1000);
        $uuid2 = Uuid::generate();

        self::assertLessThan($uuid2, $uuid1);
    }

    public function testIsValidWithValidUuids(): void
    {
        self::assertTrue(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000'));
        self::assertTrue(Uuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a'));
        self::assertTrue(Uuid::isValid('6ba7b810-9dad-11d1-80b4-00c04fd430c8'));
    }

    public function testIsValidWithInvalidUuids(): void
    {
        self::assertFalse(Uuid::isValid(''));
        self::assertFalse(Uuid::isValid('not-a-uuid'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-a716'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000-extra'));
        self::assertFalse(Uuid::isValid('550e8400e29b41d4a716446655440000'));
    }

    public function testIsValidWithSpecificVersion(): void
    {
        $v7Uuid = Uuid::generate();

        self::assertTrue(Uuid::isValid($v7Uuid, 7));
        self::assertFalse(Uuid::isValid($v7Uuid, 4));
    }

    public function testGenerateUniqueness(): void
    {
        $uuids = [];
        for ($i = 0; $i < 1000; $i++) {
            $uuids[] = Uuid::generate();
        }

        $unique = array_unique($uuids);
        self::assertCount(1000, $unique);
    }

    public function testIsValidWithAllVersions(): void
    {
        self::assertTrue(Uuid::isValid('6ba7b810-9dad-11d1-80b4-00c04fd430c8', 1));
        self::assertTrue(Uuid::isValid('000003e8-cbb4-21ed-b200-325096b39f47', 2));
        self::assertTrue(Uuid::isValid('a3bb189e-8bf9-3888-9912-ace4e6543002', 3));
        self::assertTrue(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000', 4));
        self::assertTrue(Uuid::isValid('74738ff5-5367-5958-9aee-98fffdcd1876', 5));
        self::assertTrue(Uuid::isValid('1ef0c0d5-cf31-6f45-86a9-1e2b72a3e1ef', 6));
        self::assertTrue(Uuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a', 7));
        self::assertTrue(Uuid::isValid('320c3d4d-cc00-875b-8ec9-32d5f69181c0', 8));
    }

    public function testIsValidWithWrongVersion(): void
    {
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000', 7));
        self::assertFalse(Uuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a', 4));
        self::assertFalse(Uuid::isValid('6ba7b810-9dad-11d1-80b4-00c04fd430c8', 5));
    }

    public function testIsValidWithInvalidFormat(): void
    {
        self::assertFalse(Uuid::isValid('ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000-'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-G716-446655440000'));
    }

    public function testIsValidReturnsFalseForInvalidVersionInUuid(): void
    {
        self::assertFalse(Uuid::isValid('550e8400-e29b-91d4-a716-446655440000'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-01d4-a716-446655440000'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-f1d4-a716-446655440000'));
    }

    public function testGenerateWithUppercaseValidation(): void
    {
        $uuid = Uuid::generate();
        $upperUuid = strtoupper($uuid);

        self::assertTrue(Uuid::isValid($upperUuid));
        self::assertTrue(Uuid::isValid($upperUuid, 7));
    }

    public function testIsValidWithMixedCaseUuid(): void
    {
        self::assertTrue(Uuid::isValid('550E8400-e29b-41D4-A716-446655440000'));
        self::assertTrue(Uuid::isValid('550e8400-E29B-41d4-a716-446655440000'));
    }

    public function testIsValidWithNullVersion(): void
    {
        self::assertTrue(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000', null));
        self::assertTrue(Uuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a', null));
    }

    public function testIsValidWithVersionZero(): void
    {
        self::assertFalse(Uuid::isValid('550e8400-e29b-01d4-a716-446655440000', 0));
    }

    public function testIsValidWithVersionNine(): void
    {
        self::assertFalse(Uuid::isValid('550e8400-e29b-91d4-a716-446655440000', 9));
    }

    public function testGenerateFormat(): void
    {
        $uuid = Uuid::generate();
        $parts = explode('-', $uuid);

        self::assertCount(5, $parts);
        self::assertEquals(8, strlen($parts[0]));
        self::assertEquals(4, strlen($parts[1]));
        self::assertEquals(4, strlen($parts[2]));
        self::assertEquals(4, strlen($parts[3]));
        self::assertEquals(12, strlen($parts[4]));
    }

    public function testGenerateVariantBits(): void
    {
        $uuid = Uuid::generate();
        $variantChar = substr($uuid, 19, 1);

        self::assertContains($variantChar, ['8', '9', 'a', 'b', 'A', 'B']);
    }

    public function testIsValidWithWhitespace(): void
    {
        self::assertFalse(Uuid::isValid(' 550e8400-e29b-41d4-a716-446655440000'));
        self::assertFalse(Uuid::isValid('550e8400-e29b-41d4-a716-446655440000 '));
        self::assertFalse(Uuid::isValid(' 550e8400-e29b-41d4-a716-446655440000 '));
    }

    public function testIsValidWithBraces(): void
    {
        self::assertFalse(Uuid::isValid('{550e8400-e29b-41d4-a716-446655440000}'));
    }

    public function testIsValidWithUrn(): void
    {
        self::assertFalse(Uuid::isValid('urn:uuid:550e8400-e29b-41d4-a716-446655440000'));
    }

    public function testIsValidWithNilUuid(): void
    {
        self::assertFalse(Uuid::isValid('00000000-0000-0000-0000-000000000000'));
    }

    public function testIsValidWithMaxUuid(): void
    {
        self::assertFalse(Uuid::isValid('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }
}
