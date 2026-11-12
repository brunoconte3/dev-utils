<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Fixa o comportamento do despacho de regras que a regra `requiredIf` toca
 * (campo ausente, optional, mensagem customizada e formatos de regra).
 */
class RuleCharacterizationTest extends TestCase
{
    private const MISSING_FIELD_MESSAGE = 'O campo obrigatório a não foi encontrado nos dados de entrada!';
    private const RULE_EQUALS_A = 'equals:a';

    private function validate(array $data, array $rules): array
    {
        $validator = new Validator();
        $validator->set($data, $rules);

        return $validator->getErros();
    }

    public function testMissingFieldWithRequiredReportsMissingFieldError(): void
    {
        self::assertSame(['a' => self::MISSING_FIELD_MESSAGE], $this->validate(['z' => 1], ['a' => 'required']));
    }

    public function testMissingFieldWithoutRequiredHasNoError(): void
    {
        self::assertSame([], $this->validate(['z' => 1], ['a' => 'email']));
    }

    public function testMissingFieldWithOptionalHasNoError(): void
    {
        self::assertSame([], $this->validate(['z' => 1], ['a' => 'optional|email']));
    }

    public function testMissingFieldWithRequiredFileReportsMissingFieldError(): void
    {
        self::assertSame(['a' => self::MISSING_FIELD_MESSAGE], $this->validate(['z' => 1], ['a' => 'requiredFile']));
    }

    public function testMissingFieldWithRequiredInJsonRulesReportsMissingFieldError(): void
    {
        self::assertSame(
            ['a' => self::MISSING_FIELD_MESSAGE],
            $this->validate(['z' => 1], ['a' => '{"required":true}']),
        );
    }

    public function testOptionalSkipsRulesWhenValueIsEmpty(): void
    {
        self::assertSame([], $this->validate(['a' => ''], ['a' => 'optional|email']));
    }

    public function testEmptyValueWithoutOptionalRunsTheRules(): void
    {
        self::assertSame(
            ['a' => 'O campo a deve ser um endereço de e-mail válido!'],
            $this->validate(['a' => ''], ['a' => 'email']),
        );
    }

    public function testPresentEmptyValueWithRequiredReportsMandatoryError(): void
    {
        self::assertSame(['a' => 'O campo a é obrigatório!'], $this->validate(['a' => ''], ['a' => 'required']));
    }

    public function testCommaIntroducesCustomMessage(): void
    {
        self::assertSame(['a' => 'Mensagem X'], $this->validate(['a' => '1'], ['a' => 'min:5,Mensagem X']));
    }

    public function testSemicolonSeparatesListParameters(): void
    {
        self::assertSame([], $this->validate(['a' => 'x'], ['a' => 'arrayValues:x-y']));
    }

    public function testRulesInJsonFormatAreApplied(): void
    {
        self::assertSame(
            ['a' => 'O campo a precisa conter no mínimo 5 caracteres!'],
            $this->validate(['a' => 'x'], ['a' => '{"min":5}']),
        );
    }

    public function testEqualsReceivesTheSiblingFieldsOfTheSameLevel(): void
    {
        self::assertSame([], $this->validate(['a' => 'x', 'b' => 'x'], ['b' => self::RULE_EQUALS_A]));
        self::assertSame(
            ['b' => 'O campo b é diferente do campo a!'],
            $this->validate(['a' => 'x', 'b' => 'y'], ['b' => self::RULE_EQUALS_A]),
        );
    }

    public function testListOfObjectsIsValidatedItemByItem(): void
    {
        $errors = $this->validate(
            [['a' => 'x', 'b' => 'x'], ['a' => 'x', 'b' => 'y']],
            ['b' => self::RULE_EQUALS_A],
        );

        self::assertSame(['b' => 'O campo b é diferente do campo a!'], $errors);
    }
}
