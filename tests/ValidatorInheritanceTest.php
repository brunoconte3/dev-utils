<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatorInheritanceTest extends TestCase
{
    private const FIELD = 'campo';
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
            [self::FIELD => 'O campo campo é obrigatório!'],
            $validator->getErros(),
        );
    }

    public function testChildReportsRuleWithValue(): void
    {
        $validator = self::childValidator();
        $validator->set([self::FIELD => 'ab'], [self::FIELD => self::RULE_MIN_5]);

        self::assertSame(
            [self::FIELD => 'O campo campo precisa conter no mínimo 5 caracteres!'],
            $validator->getErros(),
        );
    }

    /**
     * @return array<string, array{0: mixed, 1: string}>
     */
    public static function ruleProvider(): array
    {
        return [
            'arrayValues fora da lista' => ['X', 'arrayValues:S-N-T'],
            'dateBrazil invalido' => ['31/02/2021', 'dateBrazil'],
            'email invalido' => ['bruno', 'email'],
            'email valido' => ['bruno@teste.com', 'email'],
            'hour invalido' => ['99:99', 'hour'],
            'identifier invalido' => ['11111111111', 'identifier'],
            'int invalido' => ['abc', 'int'],
            'json de regras' => ['ab', '{"required":true,"min":5}'],
            'max excedido' => ['abcdef', 'max:3'],
            'mensagem customizada' => ['ab', 'min:5, Muito curto'],
            'min com valor curto' => ['ab', self::RULE_MIN_5],
            'min com valor ok' => ['abcdef', self::RULE_MIN_5],
            'phone invalido' => ['123', 'phone'],
            'pipe com optional' => ['', 'optional|min:5'],
            'pipe com required e min' => ['ab', 'required|min:5'],
            'regra inexistente' => ['abc', 'regraQueNaoExiste'],
            'required com nulo' => [null, 'required'],
            'required com vazio' => ['', 'required'],
            'required preenchido' => ['Bruno', 'required'],
            'upper invalido' => ['abc', 'upper'],
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
        $data = ['senha' => 'abc123', 'confirmacao' => 'abc123'];
        $rules = ['confirmacao' => 'equals:senha'];

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
        $child->set(['senha' => 'abc123', 'confirmacao' => 'outro'], ['confirmacao' => 'equals:senha']);

        self::assertArrayHasKey('confirmacao', $child->getErros());
    }
}
