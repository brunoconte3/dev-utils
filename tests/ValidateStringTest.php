<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateString;
use PHPUnit\Framework\TestCase;

class ValidateStringTest extends TestCase
{
    public function testMaxWords(): void
    {
        self::assertTrue(ValidateString::maxWords('Bruno Conte', 2));
        self::assertFalse(ValidateString::maxWords('Bruno Conte', 1));
    }

    public function testMinWords(): void
    {
        self::assertTrue(ValidateString::minWords('Bruno Conte', 2));
        self::assertFalse(ValidateString::minWords('Bruno Conte', 3));
    }

    public function testMinWordsWithEmptyString(): void
    {
        self::assertFalse(ValidateString::minWords('', 1));
        self::assertTrue(ValidateString::minWords('', 0));
    }

    public function testMaxWordsWithEmptyString(): void
    {
        self::assertTrue(ValidateString::maxWords('', 0));
        self::assertTrue(ValidateString::maxWords('', 1));
    }

    public function testMinWordsWithSingleWord(): void
    {
        self::assertTrue(ValidateString::minWords('Bruno', 1));
        self::assertFalse(ValidateString::minWords('Bruno', 2));
    }

    public function testMaxWordsWithSingleWord(): void
    {
        self::assertTrue(ValidateString::maxWords('Bruno', 1));
        self::assertTrue(ValidateString::maxWords('Bruno', 2));
        self::assertFalse(ValidateString::maxWords('Bruno', 0));
    }

    public function testMinWordsWithMultipleSpaces(): void
    {
        self::assertTrue(ValidateString::minWords('Bruno    Conte', 2));
        self::assertFalse(ValidateString::minWords('Bruno    Conte', 3));
    }

    public function testMaxWordsWithMultipleSpaces(): void
    {
        self::assertTrue(ValidateString::maxWords('Bruno    Conte', 2));
        self::assertFalse(ValidateString::maxWords('Bruno    Conte', 1));
    }

    public function testMinWordsWithLeadingAndTrailingSpaces(): void
    {
        self::assertTrue(ValidateString::minWords('   Bruno Conte   ', 2));
        self::assertFalse(ValidateString::minWords('   Bruno Conte   ', 3));
    }

    public function testMaxWordsWithLeadingAndTrailingSpaces(): void
    {
        self::assertTrue(ValidateString::maxWords('   Bruno Conte   ', 2));
        self::assertFalse(ValidateString::maxWords('   Bruno Conte   ', 1));
    }

    public function testMinWordsWithTabsAndNewlines(): void
    {
        self::assertTrue(ValidateString::minWords("Bruno\tConte\nDeveloper", 3));
        self::assertFalse(ValidateString::minWords("Bruno\tConte\nDeveloper", 4));
    }

    public function testMaxWordsWithTabsAndNewlines(): void
    {
        self::assertTrue(ValidateString::maxWords("Bruno\tConte\nDeveloper", 3));
        self::assertFalse(ValidateString::maxWords("Bruno\tConte\nDeveloper", 2));
    }

    public function testMinWordsWithExactMatch(): void
    {
        self::assertTrue(ValidateString::minWords('One Two Three', 3));
        self::assertTrue(ValidateString::minWords('One Two Three Four', 3));
    }

    public function testMaxWordsWithExactMatch(): void
    {
        self::assertTrue(ValidateString::maxWords('One Two Three', 3));
        self::assertTrue(ValidateString::maxWords('One Two', 3));
    }

    public function testMinWordsWithZero(): void
    {
        self::assertTrue(ValidateString::minWords('Any text', 0));
        self::assertTrue(ValidateString::minWords('', 0));
    }

    public function testMaxWordsWithLargeNumber(): void
    {
        self::assertTrue(ValidateString::maxWords('Bruno Conte Developer', 100));
    }

    public function testMinWordsWithSpecialCharacters(): void
    {
        self::assertTrue(ValidateString::minWords('Hello @world! #test', 3));
        self::assertFalse(ValidateString::minWords('Hello @world! #test', 4));
    }

    public function testMaxWordsWithSpecialCharacters(): void
    {
        self::assertTrue(ValidateString::maxWords('Hello @world! #test', 3));
        self::assertFalse(ValidateString::maxWords('Hello @world! #test', 2));
    }

    public function testMinWordsWithOnlySpaces(): void
    {
        self::assertFalse(ValidateString::minWords('     ', 1));
        self::assertTrue(ValidateString::minWords('     ', 0));
    }

    public function testMaxWordsWithOnlySpaces(): void
    {
        self::assertTrue(ValidateString::maxWords('     ', 0));
        self::assertTrue(ValidateString::maxWords('     ', 1));
    }

    public function testMinWordsWithNumbers(): void
    {
        self::assertTrue(ValidateString::minWords('123 456 789', 3));
        self::assertFalse(ValidateString::minWords('123 456 789', 4));
    }

    public function testMaxWordsWithNumbers(): void
    {
        self::assertTrue(ValidateString::maxWords('123 456 789', 3));
        self::assertFalse(ValidateString::maxWords('123 456 789', 2));
    }
}
