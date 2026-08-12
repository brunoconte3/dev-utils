<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\ValidateString;
use PHPUnit\Framework\TestCase;

class ValidateStringTest extends TestCase
{
    private const TEXT_TWO_WORDS = 'Bruno Conte';
    private const TEXT_MULTIPLE_SPACES = 'Bruno    Conte';
    private const TEXT_SURROUNDING_SPACES = '   Bruno Conte   ';
    private const TEXT_TABS_AND_NEWLINES = "Bruno\tConte\nDeveloper";
    private const TEXT_WITH_SYMBOLS = 'Hello @world! #test';
    private const TEXT_ONLY_SPACES = '     ';
    private const TEXT_ONLY_NUMBERS = '123 456 789';
    private const TEXT_ACCENTED = 'Olá Mundo Café';
    private const TEXT_HYPHENATED = 'well-known fact';
    private const TEXT_MIXED_WHITESPACE = "word1\t\n  word2   word3";
    private const TEXT_CARRIAGE_RETURNS = "word1\r\nword2\rword3";
    private const TEXT_WITH_EMOJI = 'Hello 👋 World';

    public function testMaxWords(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_TWO_WORDS, 2));
        self::assertFalse(ValidateString::maxWords(self::TEXT_TWO_WORDS, 1));
    }

    public function testMinWords(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_TWO_WORDS, 2));
        self::assertFalse(ValidateString::minWords(self::TEXT_TWO_WORDS, 3));
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
        self::assertTrue(ValidateString::minWords(self::TEXT_MULTIPLE_SPACES, 2));
        self::assertFalse(ValidateString::minWords(self::TEXT_MULTIPLE_SPACES, 3));
    }

    public function testMaxWordsWithMultipleSpaces(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_MULTIPLE_SPACES, 2));
        self::assertFalse(ValidateString::maxWords(self::TEXT_MULTIPLE_SPACES, 1));
    }

    public function testMinWordsWithLeadingAndTrailingSpaces(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_SURROUNDING_SPACES, 2));
        self::assertFalse(ValidateString::minWords(self::TEXT_SURROUNDING_SPACES, 3));
    }

    public function testMaxWordsWithLeadingAndTrailingSpaces(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_SURROUNDING_SPACES, 2));
        self::assertFalse(ValidateString::maxWords(self::TEXT_SURROUNDING_SPACES, 1));
    }

    public function testMinWordsWithTabsAndNewlines(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_TABS_AND_NEWLINES, 3));
        self::assertFalse(ValidateString::minWords(self::TEXT_TABS_AND_NEWLINES, 4));
    }

    public function testMaxWordsWithTabsAndNewlines(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_TABS_AND_NEWLINES, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_TABS_AND_NEWLINES, 2));
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
        self::assertTrue(ValidateString::minWords(self::TEXT_WITH_SYMBOLS, 3));
        self::assertFalse(ValidateString::minWords(self::TEXT_WITH_SYMBOLS, 4));
    }

    public function testMaxWordsWithSpecialCharacters(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_WITH_SYMBOLS, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_WITH_SYMBOLS, 2));
    }

    public function testMinWordsWithOnlySpaces(): void
    {
        self::assertFalse(ValidateString::minWords(self::TEXT_ONLY_SPACES, 1));
        self::assertTrue(ValidateString::minWords(self::TEXT_ONLY_SPACES, 0));
    }

    public function testMaxWordsWithOnlySpaces(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_ONLY_SPACES, 0));
        self::assertTrue(ValidateString::maxWords(self::TEXT_ONLY_SPACES, 1));
    }

    public function testMinWordsWithNumbers(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_ONLY_NUMBERS, 3));
        self::assertFalse(ValidateString::minWords(self::TEXT_ONLY_NUMBERS, 4));
    }

    public function testMaxWordsWithNumbers(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_ONLY_NUMBERS, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_ONLY_NUMBERS, 2));
    }

    public function testMinWordsWithUnicodeCharacters(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_ACCENTED, 3));
        self::assertFalse(ValidateString::minWords('Olá Mundo', 3));
    }

    public function testMaxWordsWithUnicodeCharacters(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_ACCENTED, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_ACCENTED, 2));
    }

    public function testMinWordsWithHyphenatedWords(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_HYPHENATED, 2));
        self::assertFalse(ValidateString::minWords(self::TEXT_HYPHENATED, 3));
    }

    public function testMaxWordsWithHyphenatedWords(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_HYPHENATED, 2));
        self::assertFalse(ValidateString::maxWords(self::TEXT_HYPHENATED, 1));
    }

    public function testMinWordsWithMixedWhitespace(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_MIXED_WHITESPACE, 3));
        self::assertFalse(ValidateString::minWords(self::TEXT_MIXED_WHITESPACE, 4));
    }

    public function testMaxWordsWithMixedWhitespace(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_MIXED_WHITESPACE, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_MIXED_WHITESPACE, 2));
    }

    public function testMinWordsWithVeryLongText(): void
    {
        $words = implode(' ', array_fill(0, 100, 'word'));
        self::assertTrue(ValidateString::minWords($words, 100));
        self::assertTrue(ValidateString::minWords($words, 50));
        self::assertFalse(ValidateString::minWords($words, 101));
    }

    public function testMaxWordsWithVeryLongText(): void
    {
        $words = implode(' ', array_fill(0, 100, 'word'));
        self::assertTrue(ValidateString::maxWords($words, 100));
        self::assertTrue(ValidateString::maxWords($words, 150));
        self::assertFalse(ValidateString::maxWords($words, 99));
    }

    public function testMinWordsWithCarriageReturn(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_CARRIAGE_RETURNS, 3));
        self::assertFalse(ValidateString::minWords("word1\r\nword2", 3));
    }

    public function testMaxWordsWithCarriageReturn(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_CARRIAGE_RETURNS, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_CARRIAGE_RETURNS, 2));
    }

    public function testMinWordsWithEmoji(): void
    {
        self::assertTrue(ValidateString::minWords(self::TEXT_WITH_EMOJI, 3));
        self::assertFalse(ValidateString::minWords('Hello 👋', 3));
    }

    public function testMaxWordsWithEmoji(): void
    {
        self::assertTrue(ValidateString::maxWords(self::TEXT_WITH_EMOJI, 3));
        self::assertFalse(ValidateString::maxWords(self::TEXT_WITH_EMOJI, 2));
    }

    public function testMinWordsWithPunctuation(): void
    {
        self::assertTrue(ValidateString::minWords('Hello, World! How are you?', 5));
        self::assertFalse(ValidateString::minWords('Hello, World!', 3));
    }

    public function testMaxWordsWithPunctuation(): void
    {
        self::assertTrue(ValidateString::maxWords('Hello, World!', 2));
        self::assertFalse(ValidateString::maxWords('Hello, World! Test', 2));
    }
}
