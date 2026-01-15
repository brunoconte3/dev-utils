<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateUuid;
use PHPUnit\Framework\TestCase;

final class ValidateUuidTest extends TestCase
{
    public function testValidV4(): void
    {
        self::assertTrue(ValidateUuid::isValid('550e8400-e29b-41d4-a716-446655440000'));
        self::assertTrue(ValidateUuid::isValid('550E8400-E29B-41D4-A716-446655440000'));
    }

    public function testValidV7(): void
    {
        self::assertTrue(ValidateUuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a'));
    }

    public function testRejectWrongVersion(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-61d4-a716-446655440000'));
    }

    public function testRejectWrongVariant(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-c716-446655440000'));
    }

    public function testRejectInvalidFormat(): void
    {
        self::assertFalse(ValidateUuid::isValid(''));
        self::assertFalse(ValidateUuid::isValid('not-a-uuid'));
        self::assertFalse(ValidateUuid::isValid('550e8400e29b41d4a716446655440000'));
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-44665544ZZZZ'));
    }

    public function testValidV4LowerCase(): void
    {
        self::assertTrue(ValidateUuid::isValid('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'));
    }

    public function testValidV4UpperCase(): void
    {
        self::assertTrue(ValidateUuid::isValid('A0EEBC99-9C0B-4EF8-BB6D-6BB9BD380A11'));
    }

    public function testValidV4MixedCase(): void
    {
        self::assertTrue(ValidateUuid::isValid('A0eeBC99-9c0B-4eF8-Bb6D-6Bb9BD380a11'));
    }

    public function testValidV7MixedCase(): void
    {
        self::assertTrue(ValidateUuid::isValid('01890F87-4f0B-7F6B-8b1D-9f4F9D7C3B5A'));
    }

    public function testRejectV1(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-11d4-a716-446655440000'));
    }

    public function testRejectV3(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-31d4-a716-446655440000'));
    }

    public function testRejectV5(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-51d4-a716-446655440000'));
    }

    public function testRejectV6(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-61d4-a716-446655440000'));
    }

    public function testRejectV8(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-81d4-a716-446655440000'));
    }

    public function testRejectInvalidVariantC(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-c716-446655440000'));
    }

    public function testRejectInvalidVariantD(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-d716-446655440000'));
    }

    public function testRejectInvalidVariantE(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-e716-446655440000'));
    }

    public function testRejectInvalidVariantF(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-f716-446655440000'));
    }

    public function testRejectTooShort(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-4466554400'));
    }

    public function testRejectTooLong(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-446655440000a'));
    }

    public function testRejectWithSpaces(): void
    {
        self::assertFalse(ValidateUuid::isValid(' 550e8400-e29b-41d4-a716-446655440000'));
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-446655440000 '));
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b -41d4-a716-446655440000'));
    }

    public function testRejectWithBraces(): void
    {
        self::assertFalse(ValidateUuid::isValid('{550e8400-e29b-41d4-a716-446655440000}'));
    }

    public function testRejectWithUrn(): void
    {
        self::assertFalse(ValidateUuid::isValid('urn:uuid:550e8400-e29b-41d4-a716-446655440000'));
    }

    public function testRejectNilUuid(): void
    {
        self::assertFalse(ValidateUuid::isValid('00000000-0000-0000-0000-000000000000'));
    }

    public function testRejectMaxUuid(): void
    {
        self::assertFalse(ValidateUuid::isValid('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testRejectWrongHyphenPosition(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e84-00e29b-41d4-a716-446655440000'));
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b41d4-a716-446655440000'));
    }

    public function testRejectOnlyHyphens(): void
    {
        self::assertFalse(ValidateUuid::isValid('------------------------------------'));
    }

    public function testRejectSpecialCharacters(): void
    {
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-44665544000!'));
        self::assertFalse(ValidateUuid::isValid('550e8400-e29b-41d4-a716-44665544000@'));
    }

    public function testValidVariantA(): void
    {
        self::assertTrue(ValidateUuid::isValid('550e8400-e29b-41d4-a716-446655440000'));
    }

    public function testValidVariantB(): void
    {
        self::assertTrue(ValidateUuid::isValid('550e8400-e29b-41d4-b716-446655440000'));
    }

    public function testValidVariant8(): void
    {
        self::assertTrue(ValidateUuid::isValid('550e8400-e29b-41d4-8716-446655440000'));
    }

    public function testValidVariant9(): void
    {
        self::assertTrue(ValidateUuid::isValid('550e8400-e29b-41d4-9716-446655440000'));
    }
}
