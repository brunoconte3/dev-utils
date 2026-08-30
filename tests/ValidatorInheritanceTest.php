<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatorInheritanceTest extends TestCase
{
    private const FIELD = 'field';
    private const RULE_MIN_5 = 'min:5';

    private static function childValidator(): Validator
    {
        return new class extends Validator {
        };
    }

    public function testChildClassNameIsLongerThanTheParent(): void
    {
        self::assertNotSame(Validator::class, self::childValidator()::class);
        self::assertGreaterThan(strlen(Validator::class), strlen(self::childValidator()::class));
    }

    public function testChildReportsMandatoryField(): void
    {
        $validator = self::childValidator();
        $validator->set([self::FIELD => ''], [self::FIELD => 'required']);

        self::assertSame(
            [self::FIELD => 'O campo field é obrigatório!'],
            $validator->getErros(),
        );
    }

    public function testChildReportsRuleWithValue(): void
    {
        $validator = self::childValidator();
        $validator->set([self::FIELD => 'ab'], [self::FIELD => self::RULE_MIN_5]);

        self::assertSame(
            [self::FIELD => 'O campo field precisa conter no mínimo 5 caracteres!'],
            $validator->getErros(),
        );
    }

    /**
     * @return array<string, array{0: mixed, 1: string}>
     */
    public static function ruleProvider(): array
    {
        return [
            'arrayValues out of the list' => ['X', 'arrayValues:S-N-T'],
            'invalid dateBrazil' => ['31/02/2021', 'dateBrazil'],
            'invalid email' => ['bruno', 'email'],
            'valid email' => ['bruno@teste.com', 'email'],
            'invalid hour' => ['99:99', 'hour'],
            'invalid identifier' => ['11111111111', 'identifier'],
            'invalid int' => ['abc', 'int'],
            'json rules' => ['ab', '{"required":true,"min":5}'],
            'max exceeded' => ['abcdef', 'max:3'],
            'custom message' => ['ab', 'min:5, Muito curto'],
            'min with short value' => ['ab', self::RULE_MIN_5],
            'min with ok value' => ['abcdef', self::RULE_MIN_5],
            'invalid phone' => ['123', 'phone'],
            'pipe with optional' => ['', 'optional|min:5'],
            'pipe with required and min' => ['ab', 'required|min:5'],
            'non existent rule' => ['abc', 'ruleThatDoesNotExist'],
            'required with null' => [null, 'required'],
            'required with empty' => ['', 'required'],
            'required filled' => ['Bruno', 'required'],
            'invalid upper' => ['abc', 'upper'],
        ];
    }

    #[DataProvider('ruleProvider')]
    public function testChildBehavesLikeValidator(mixed $value, string $rule): void
    {
        $data = [self::FIELD => $value];

        $parent = new Validator();
        $parent->set($data, [self::FIELD => $rule]);

        $child = self::childValidator();
        $child->set($data, [self::FIELD => $rule]);

        self::assertSame($parent->getErros(), $child->getErros());
    }

    public function testChildHandlesEqualsRuleThatNeedsSiblingData(): void
    {
        $data = ['password' => 'abc123', 'confirmation' => 'abc123'];
        $rules = ['confirmation' => 'equals:password'];

        $parent = new Validator();
        $parent->set($data, $rules);

        $child = self::childValidator();
        $child->set($data, $rules);

        self::assertSame([], $child->getErros());
        self::assertSame($parent->getErros(), $child->getErros());
    }

    public function testChildReportsMismatchOnEqualsRule(): void
    {
        $child = self::childValidator();
        $child->set(['password' => 'abc123', 'confirmation' => 'other'], ['confirmation' => 'equals:password']);

        self::assertArrayHasKey('confirmation', $child->getErros());
    }
}
