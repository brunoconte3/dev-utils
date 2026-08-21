<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateUuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidateUuid::class)]
final class ValidateUuidTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function acceptedUuidProvider(): array
    {
        return [
            'v4 lower case' => ['a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'],
            'v4 mixed case' => ['A0eeBC99-9c0B-4eF8-Bb6D-6Bb9BD380a11'],
            'v4 upper case' => ['A0EEBC99-9C0B-4EF8-BB6D-6BB9BD380A11'],
            'v4 variant 8' => ['550e8400-e29b-41d4-8716-446655440000'],
            'v4 variant 9' => ['550e8400-e29b-41d4-9716-446655440000'],
            'v4 variant a' => ['550e8400-e29b-41d4-a716-446655440000'],
            'v4 variant b' => ['550e8400-e29b-41d4-b716-446655440000'],
            'v7 lower case' => ['01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a'],
            'v7 mixed case' => ['01890F87-4f0B-7F6B-8b1D-9f4F9D7C3B5A'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rejectedVersionProvider(): array
    {
        return [
            'max uuid' => ['ffffffff-ffff-ffff-ffff-ffffffffffff'],
            'nil uuid' => ['00000000-0000-0000-0000-000000000000'],
            'v1' => ['550e8400-e29b-11d4-a716-446655440000'],
            'v2' => ['550e8400-e29b-21d4-a716-446655440000'],
            'v3' => ['550e8400-e29b-31d4-a716-446655440000'],
            'v5' => ['550e8400-e29b-51d4-a716-446655440000'],
            'v6' => ['550e8400-e29b-61d4-a716-446655440000'],
            'v8' => ['550e8400-e29b-81d4-a716-446655440000'],
            'version 9' => ['550e8400-e29b-91d4-a716-446655440000'],
            'version f' => ['550e8400-e29b-f1d4-a716-446655440000'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rejectedVariantProvider(): array
    {
        return [
            'variant 0' => ['550e8400-e29b-41d4-0716-446655440000'],
            'variant c' => ['550e8400-e29b-41d4-c716-446655440000'],
            'variant d' => ['550e8400-e29b-41d4-d716-446655440000'],
            'variant e' => ['550e8400-e29b-41d4-e716-446655440000'],
            'variant f' => ['550e8400-e29b-41d4-f716-446655440000'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rejectedFormatProvider(): array
    {
        return [
            'braces' => ['{550e8400-e29b-41d4-a716-446655440000}'],
            'empty' => [''],
            'hyphens only' => ['------------------------------------'],
            'inner space' => ['550e8400-e29b -41d4-a716-446655440000'],
            'leading space' => [' 550e8400-e29b-41d4-a716-446655440000'],
            'missing hyphens' => ['550e8400e29b41d4a716446655440000'],
            'non hex chars' => ['550e8400-e29b-41d4-a716-44665544ZZZZ'],
            'single zero' => ['0'],
            'special char at' => ['550e8400-e29b-41d4-a716-44665544000@'],
            'special char bang' => ['550e8400-e29b-41d4-a716-44665544000!'],
            'too long' => ['550e8400-e29b-41d4-a716-446655440000a'],
            'too short' => ['550e8400-e29b-41d4-a716-4466554400'],
            'trailing carriage return' => ["550e8400-e29b-41d4-a716-446655440000\r"],
            'trailing line feed' => ["550e8400-e29b-41d4-a716-446655440000\n"],
            'trailing space' => ['550e8400-e29b-41d4-a716-446655440000 '],
            'urn prefix' => ['urn:uuid:550e8400-e29b-41d4-a716-446655440000'],
            'words' => ['not-a-uuid'],
            'wrong hyphen position' => ['550e84-00e29b-41d4-a716-446655440000'],
            'wrong hyphen position on third group' => ['550e8400-e29b41d4-a716-446655440000'],
        ];
    }

    #[DataProvider('acceptedUuidProvider')]
    public function testIsValidAcceptsV4AndV7(string $uuid): void
    {
        self::assertTrue(ValidateUuid::isValid($uuid));
    }

    #[DataProvider('rejectedVersionProvider')]
    public function testIsValidRejectsOtherVersions(string $uuid): void
    {
        self::assertFalse(ValidateUuid::isValid($uuid));
    }

    #[DataProvider('rejectedVariantProvider')]
    public function testIsValidRejectsNonRfcVariant(string $uuid): void
    {
        self::assertFalse(ValidateUuid::isValid($uuid));
    }

    #[DataProvider('rejectedFormatProvider')]
    public function testIsValidRejectsMalformedInput(string $uuid): void
    {
        self::assertFalse(ValidateUuid::isValid($uuid));
    }
}
