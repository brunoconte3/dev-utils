<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

class RuleFileCharacterizationTest extends TestCase
{
    private const FIELD = 'upload';
    private const MIME_PNG = 'image/png';
    private const MIME_TEXT = 'text/plain';
    private const NOT_IMAGE_MESSAGE = 'Para validar minWidth, maxWidth, minHeight e maxHeight '
        . 'o arquivo precisa ser uma imagem!';

    private array $filesBackup = [];

    protected function setUp(): void
    {
        $this->filesBackup = $_FILES;
        $_FILES = [];
    }

    protected function tearDown(): void
    {
        $_FILES = $this->filesBackup;
    }

    private function validate(array $data, array $rules): array
    {
        $validator = new Validator();
        $validator->set($data, $rules);

        return $validator->getErros();
    }

    private function imageFile(): array
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'static'
            . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'iconTest.png';

        return [
            'error' => 0,
            'name' => 'iconTest.png',
            'size' => 0,
            'tmp_name' => $path,
            'type' => self::MIME_PNG,
        ];
    }

    private function parameterMessage(string $label): string
    {
        return "O parâmetro do validador '$label', deve ser numérico e maior ou igual a zero!";
    }

    private function expectedGuardMessage(): string
    {
        return extension_loaded('gd') ? 'Anexo não foi encontrado!' : 'Biblioteca GD não foi encontrada!';
    }

    public static function invalidParameterProvider(): array
    {
        return [
            'maxFile text' => ['maxFile:abc', 'maxFile'],
            'maxFile zero' => ['maxFile:0', 'maxFile'],
            'maxUploadSize zero' => ['maxUploadSize:0', 'maxUploadSize'],
            'minFile zero' => ['minFile:0', 'minFile'],
            'minUploadSize text' => ['minUploadSize:abc', 'minUploadSize'],
            'minUploadSize zero' => ['minUploadSize:0', 'minUploadSize'],
        ];
    }

    #[DataProvider('invalidParameterProvider')]
    public function testInvalidRuleParameterReportsValidatorLabelAtIndexZero(string $rule, string $label): void
    {
        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => $rule]);

        self::assertArrayHasKey(self::FIELD, $errors);
        self::assertIsArray($errors[self::FIELD]);
        self::assertSame($this->parameterMessage($label), $errors[self::FIELD][0]);
    }

    #[DataProvider('invalidParameterProvider')]
    public function testInvalidRuleParameterReplacesEarlierStringError(string $rule, string $label): void
    {
        $errors = $this->validate(
            [self::FIELD => $this->imageFile(), 'other' => 'x'],
            [self::FIELD => 'equals:other|' . $rule],
        );

        self::assertIsArray($errors[self::FIELD]);
        self::assertSame($this->parameterMessage($label), $errors[self::FIELD][0]);
    }

    public function testInvalidParameterWithoutOtherViolationsReportsOnlyTheParameterMessage(): void
    {
        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minUploadSize:0']);

        self::assertSame([self::FIELD => [$this->parameterMessage('minUploadSize')]], $errors);
    }

    public function testFileNameWithEmptyValueReportsDefaultMessage(): void
    {
        $errors = $this->validate([self::FIELD => []], [self::FIELD => 'fileName']);

        self::assertSame([self::FIELD => ['O campo upload não pode ser vazio!']], $errors);
    }

    public function testFileNameWithEmptyValueReportsCustomMessage(): void
    {
        $errors = $this->validate([self::FIELD => []], [self::FIELD => 'fileName,Mensagem customizada']);

        self::assertSame([self::FIELD => ['Mensagem customizada']], $errors);
    }

    public function testFileNameWithEmptyValueReplacesEarlierStringError(): void
    {
        $errors = $this->validate(
            [self::FIELD => [], 'other' => 'x'],
            [self::FIELD => 'equals:other|fileName'],
        );

        self::assertSame([self::FIELD => ['O campo upload não pode ser vazio!']], $errors);
    }

    public static function dimensionRuleProvider(): array
    {
        return [
            'maxHeight' => ['maxHeight:100'],
            'maxWidth' => ['maxWidth:100'],
            'minHeight' => ['minHeight:100'],
            'minWidth' => ['minWidth:100'],
        ];
    }

    #[DataProvider('dimensionRuleProvider')]
    public function testDimensionRuleWithoutUploadReportsGuardMessage(string $rule): void
    {
        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => $rule]);

        self::assertSame([self::FIELD => [$this->expectedGuardMessage()]], $errors);
    }

    #[DataProvider('dimensionRuleProvider')]
    public function testDimensionRuleGuardMessageReplacesEarlierStringError(string $rule): void
    {
        $errors = $this->validate(
            [self::FIELD => $this->imageFile(), 'other' => 'x'],
            [self::FIELD => 'equals:other|' . $rule],
        );

        self::assertSame([self::FIELD => [$this->expectedGuardMessage()]], $errors);
    }

    #[RequiresPhpExtension('gd')]
    public function testDimensionRuleRejectsNonImageType(): void
    {
        $_FILES[self::FIELD] = ['type' => self::MIME_TEXT];

        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minWidth:100']);

        self::assertSame([self::FIELD => [self::NOT_IMAGE_MESSAGE]], $errors);
    }

    #[RequiresPhpExtension('gd')]
    public function testDimensionRuleRejectsListWithNonImageType(): void
    {
        $_FILES[self::FIELD] = ['type' => [self::MIME_PNG, self::MIME_TEXT]];

        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minWidth:100']);

        self::assertSame([self::FIELD => [self::NOT_IMAGE_MESSAGE]], $errors);
    }

    #[RequiresPhpExtension('gd')]
    public function testDimensionRuleAcceptsListOfImageTypesAndValidatesDimension(): void
    {
        $_FILES[self::FIELD] = ['type' => [self::MIME_PNG, '']];

        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minWidth:500']);

        self::assertSame(
            [self::FIELD => ['O campo upload não pode ser menor que 500 pexels de comprimento!']],
            $errors,
        );
    }

    #[RequiresPhpExtension('gd')]
    public function testDimensionRuleSkipsTypeCheckWhenUploadEntryIsNotIterable(): void
    {
        $_FILES[self::FIELD] = 'not-iterable';

        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minWidth:500']);

        self::assertSame(
            [self::FIELD => ['O campo upload não pode ser menor que 500 pexels de comprimento!']],
            $errors,
        );
    }

    #[RequiresPhpExtension('gd')]
    public function testDimensionRuleWithValidImagePasses(): void
    {
        $_FILES[self::FIELD] = ['type' => self::MIME_PNG];

        self::assertSame([], $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => 'minWidth:100']));
    }

    public static function invalidDimensionParameterProvider(): array
    {
        return [
            'maxHeight' => ['maxHeight:0', 'maxHeight', 'maior', 'altura'],
            'maxWidth reports minWidth label' => ['maxWidth:0', 'minWidth', 'maior', 'comprimento'],
            'minHeight' => ['minHeight:0', 'minHeight', '', ''],
            'minWidth' => ['minWidth:0', 'minWidth', '', ''],
        ];
    }

    #[RequiresPhpExtension('gd')]
    #[DataProvider('invalidDimensionParameterProvider')]
    public function testInvalidDimensionParameterReportsLabelAndKeepsDimensionErrors(
        string $rule,
        string $label,
        string $comparison,
        string $dimension,
    ): void {
        $_FILES[self::FIELD] = ['type' => self::MIME_PNG];

        $errors = $this->validate([self::FIELD => $this->imageFile()], [self::FIELD => $rule]);

        $expected = [$this->parameterMessage($label)];
        if ($comparison !== '') {
            $expected[] = "O campo upload não pode ser $comparison que 0 pexels de $dimension!";
        }

        self::assertSame([self::FIELD => $expected], $errors);
    }
}
