<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\resource\DateNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateNormalizerTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function convertibleDateProvider(): array
    {
        return [
            'american iso' => ['2020-10-31', '2020-10-31'],
            'american slash' => ['10/31/2020', '2020-10-31'],
            'brazilian' => ['31/10/2020', '2020-10-31'],
            'brazilian preferred when ambiguous' => ['05/06/2024', '2024-06-05'],
            'brazilian single digit parts' => ['1/1/2020', '2020-1-1'],
            'leap day' => ['29/02/2020', '2020-02-29'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unconvertibleDateProvider(): array
    {
        return [
            'dashed brazilian' => ['31-10-2020'],
            'empty' => [''],
            'impossible day' => ['31/02/2020'],
            'impossible month' => ['2020-13-01'],
            'leading space' => [' 31/10/2020'],
            'not a date' => ['xx'],
            'slash iso' => ['2020/10/31'],
            'two digit year' => ['31/10/20'],
            'unseparated digits' => ['31102020'],
        ];
    }

    #[DataProvider('convertibleDateProvider')]
    public function testToIsoDateConvertsSupportedFormats(string $date, string $expected): void
    {
        self::assertSame($expected, DateNormalizer::toIsoDate($date));
    }

    #[DataProvider('unconvertibleDateProvider')]
    public function testToIsoDateReturnsNullForUnsupportedInput(string $date): void
    {
        self::assertNull(DateNormalizer::toIsoDate($date));
    }
}
