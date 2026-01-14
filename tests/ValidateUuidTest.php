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
}
