<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RuleCustomMessageTest extends TestCase
{
    private const CUSTOM_MESSAGE = 'Mensagem customizada';
    private const FIELD = 'field';

    private function validate(array $data, array $rules): array
    {
        $validator = new Validator();
        $validator->set($data, $rules);

        return $validator->getErros();
    }

    public static function failingRuleProvider(): array
    {
        return [
            'alpha' => ['alpha', '123'],
            'alphaNoSpecial' => ['alphaNoSpecial', 'ção1'],
            'alphaNum' => ['alphaNum', 'a-b'],
            'alphaNumNoSpecial' => ['alphaNumNoSpecial', 'a-b'],
            'bool' => ['bool', 'abc'],
            'companyIdentification' => ['companyIdentification', '123'],
            'dateAmerican' => ['dateAmerican', '2020-13-45'],
            'dateBrazil' => ['dateBrazil', '32/13/2020'],
            'ddd length' => ['ddd', '1'],
            'ddd nonexistent' => ['ddd', '00'],
            'ddd state mismatch' => ['ddd:sp', '21'],
            'email' => ['email', 'abc'],
            'float' => ['float', 'abc'],
            'hour' => ['hour', '25:00'],
            'identifier' => ['identifier', '123'],
            'ip' => ['ip', 'abc'],
            'json scalar' => ['json', 5],
            'json string' => ['json', 'abc'],
            'lower' => ['lower', 'ABC'],
            'mac' => ['mac', 'zz'],
            'max' => ['max:2', 'abc'],
            'maxWords' => ['maxWords:1', 'a b'],
            'min' => ['min:5', 'abc'],
            'minWords' => ['minWords:3', 'a b'],
            'numMax above' => ['numMax:5', '10'],
            'numMax negative' => ['numMax:5', '-1'],
            'numMin below' => ['numMin:5', '1'],
            'numMin negative' => ['numMin:5', '-3'],
            'numMonth invalid' => ['numMonth', '13'],
            'numMonth not numeric' => ['numMonth', 'abc'],
            'plate' => ['plate', 'ABC'],
            'regex' => ['regex:/^a$/', 'b'],
            'upper' => ['upper', 'abc'],
            'url' => ['url', 'abc'],
            'zipcode' => ['zipcode', '123'],
        ];
    }

    #[DataProvider('failingRuleProvider')]
    public function testCustomMessageReplacesDefaultMessage(string $rule, mixed $value): void
    {
        $errors = $this->validate([self::FIELD => $value], [self::FIELD => $rule . ',' . self::CUSTOM_MESSAGE]);

        self::assertSame([self::FIELD => self::CUSTOM_MESSAGE], $errors);
    }

    #[DataProvider('failingRuleProvider')]
    public function testDefaultMessageIsUsedWithoutCustomMessage(string $rule, mixed $value): void
    {
        $errors = $this->validate([self::FIELD => $value], [self::FIELD => $rule]);

        self::assertArrayHasKey(self::FIELD, $errors);
        self::assertIsString($errors[self::FIELD]);
        self::assertNotSame('', $errors[self::FIELD]);
        self::assertNotSame(self::CUSTOM_MESSAGE, $errors[self::FIELD]);
    }

    public function testEqualsWithDifferentValueUsesCustomMessage(): void
    {
        $errors = $this->validate(
            [self::FIELD => 'x', 'other' => 'y'],
            [self::FIELD => 'equals:other,' . self::CUSTOM_MESSAGE],
        );

        self::assertSame([self::FIELD => self::CUSTOM_MESSAGE], $errors);
    }

    public function testEqualsWithUnknownReferenceFieldUsesCustomMessage(): void
    {
        $errors = $this->validate(
            [self::FIELD => 'x'],
            [self::FIELD => 'equals:unknown,' . self::CUSTOM_MESSAGE],
        );

        self::assertSame([self::FIELD => self::CUSTOM_MESSAGE], $errors);
    }

    public function testEqualsWithUnknownReferenceFieldUsesDefaultMessage(): void
    {
        $errors = $this->validate([self::FIELD => 'x'], [self::FIELD => 'equals:unknown']);

        self::assertSame(
            [self::FIELD => 'Uma regra inválida está sendo aplicada no campo field, verifique a documentação!'],
            $errors,
        );
    }

    public static function emptyMessageProvider(): array
    {
        return [
            'empty string' => [''],
            'zero string' => ['0'],
        ];
    }

    #[DataProvider('emptyMessageProvider')]
    public function testEmptyCustomMessageFallsBackToDefaultMessage(string $message): void
    {
        $errors = $this->validate(
            [self::FIELD => '123'],
            [self::FIELD => json_encode(['alpha' => true, 'message' => $message], JSON_THROW_ON_ERROR)],
        );

        self::assertSame(
            [self::FIELD => 'O campo field só pode conter caracteres alfabéticos!'],
            $errors,
        );
    }
}
