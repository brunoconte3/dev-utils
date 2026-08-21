<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\DependencyInjection\Rules;
use DevUtils\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rules::class)]
final class RulesErrorAggregationTest extends TestCase
{
    private const FIELD = 'arquivo';
    private const FILE_NAME = 'JPG - Validação upload v.1.jpg';
    private const RULE_MAX_SIZE = 'maxUploadSize:5550';
    private const ERROR_MAX_SIZE = 'O arquivo JPG - Validação upload v.1.jpg deve conter, no máximo 5550 bytes!';
    private const ERROR_EXTENSION = 'O arquivo JPG - Validação upload v.1.jpg, contém uma extensão inválida!';

    private static function oversizedJpegFile(): array
    {
        return [
            'error' => 0,
            'name' => self::FILE_NAME,
            'size' => 8488,
            'tmp_name' => '/tmp/phpODnLGo',
            'type' => 'image/jpeg',
        ];
    }

    private static function errorsFor(string $rules): array
    {
        $validator = new Validator();
        $validator->set([self::FIELD => self::oversizedJpegFile()], [self::FIELD => $rules]);

        return $validator->getErros();
    }

    public function testStoresErrorOfSingleRuleAsList(): void
    {
        self::assertSame(
            [self::FIELD => [self::ERROR_MAX_SIZE]],
            self::errorsFor(self::RULE_MAX_SIZE),
        );
    }

    public function testAccumulatesErrorsOfDifferentRulesInTheSameField(): void
    {
        self::assertSame(
            [self::FIELD => [self::ERROR_MAX_SIZE, self::ERROR_EXTENSION]],
            self::errorsFor(self::RULE_MAX_SIZE . '|mimeType:jpeg;png'),
        );
    }

    public function testDeduplicatesRepeatedErrorsInTheSameField(): void
    {
        self::assertSame(
            [self::FIELD => [self::ERROR_MAX_SIZE]],
            self::errorsFor(self::RULE_MAX_SIZE . '|' . self::RULE_MAX_SIZE),
        );
    }
}
