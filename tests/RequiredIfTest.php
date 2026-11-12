<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class RequiredIfTest extends TestCase
{
    private const RULE_CANCELED = 'requiredIf:status;cancelado';
    private const MESSAGE_MANDATORY = 'O campo motivo é obrigatório!';
    private const MESSAGE_MISSING = 'O campo obrigatório motivo não foi encontrado nos dados de entrada!';
    private const MESSAGE_INVALID_RULE =
    'Uma regra inválida está sendo aplicada no campo motivo, verifique a documentação!';
    private const RULE_EMAIL_SUFFIX = '|email';
    private const RULE_TYPE_TWO_OR_THREE = 'requiredIf:tipo;2;3';

    private function validate(array $data, array $rules): array
    {
        $validator = new Validator();
        $validator->set($data, $rules);

        return $validator->getErros();
    }

    private function validateReason(array $data, string $rule = self::RULE_CANCELED): array
    {
        return $this->validate($data, ['motivo' => $rule]);
    }

    public function testConditionMetAndFieldFilledHasNoError(): void
    {
        self::assertSame([], $this->validateReason(['status' => 'cancelado', 'motivo' => 'Desistiu']));
    }

    public function testConditionMetAndFieldEmptyReportsMandatoryError(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MANDATORY],
            $this->validateReason(['status' => 'cancelado', 'motivo' => '']),
        );
    }

    public function testConditionMetAndFieldNullReportsMandatoryError(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MANDATORY],
            $this->validateReason(['status' => 'cancelado', 'motivo' => null]),
        );
    }

    public function testConditionMetAndFieldBlankReportsMandatoryError(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MANDATORY],
            $this->validateReason(['status' => 'cancelado', 'motivo' => '   ']),
        );
    }

    public function testConditionMetAndFieldZeroCountsAsFilled(): void
    {
        self::assertSame([], $this->validateReason(['status' => 'cancelado', 'motivo' => '0']));
    }

    public function testConditionMetAndFieldMissingReportsMissingFieldError(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['status' => 'cancelado']),
        );
    }

    public function testConditionNotMetAndFieldMissingHasNoError(): void
    {
        self::assertSame([], $this->validateReason(['status' => 'ativo']));
    }

    public function testConditionNotMetAndFieldEmptyHasNoError(): void
    {
        self::assertSame([], $this->validateReason(['status' => 'ativo', 'motivo' => '']));
        self::assertSame([], $this->validateReason(['status' => 'ativo', 'motivo' => null]));
    }

    public function testConditionNotMetSkipsOtherRulesWhenFieldIsEmpty(): void
    {
        $rule = self::RULE_CANCELED . self::RULE_EMAIL_SUFFIX;

        self::assertSame([], $this->validateReason(['status' => 'ativo', 'motivo' => ''], $rule));
        self::assertSame([], $this->validateReason(['status' => 'ativo'], $rule));
    }

    public function testConditionNotMetStillAppliesOtherRulesWhenFieldIsFilled(): void
    {
        $errors = $this->validateReason(
            ['status' => 'ativo', 'motivo' => 'invalido'],
            self::RULE_CANCELED . self::RULE_EMAIL_SUFFIX
        );

        self::assertSame(['motivo' => 'O campo motivo deve ser um endereço de e-mail válido!'], $errors);
    }

    public function testConditionMetStillAppliesOtherRulesWhenFieldIsFilled(): void
    {
        $rule = self::RULE_CANCELED . '|min:10';

        self::assertSame([], $this->validateReason(['status' => 'cancelado', 'motivo' => 'Texto bem comprido'], $rule));
        self::assertSame(
            ['motivo' => 'O campo motivo precisa conter no mínimo 10 caracteres!'],
            $this->validateReason(['status' => 'cancelado', 'motivo' => 'curto'], $rule),
        );
    }

    public function testConditionMetWithEmptyFieldDoesNotRunOtherRules(): void
    {
        $errors = $this->validateReason(
            ['status' => 'cancelado', 'motivo' => ''],
            self::RULE_CANCELED . self::RULE_EMAIL_SUFFIX
        );

        self::assertSame(['motivo' => self::MESSAGE_MANDATORY], $errors);
    }

    public function testAnyOfTheListedValuesActivatesTheRule(): void
    {
        $rule = 'requiredIf:status;cancelado;suspenso';

        self::assertSame(['motivo' => self::MESSAGE_MISSING], $this->validateReason(['status' => 'cancelado'], $rule));
        self::assertSame(['motivo' => self::MESSAGE_MISSING], $this->validateReason(['status' => 'suspenso'], $rule));
        self::assertSame([], $this->validateReason(['status' => 'ativo'], $rule));
    }

    public function testListedValuesAreTrimmed(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['status' => 'suspenso'], 'requiredIf:status; cancelado ; suspenso'),
        );
    }

    public function testComparisonIsCaseSensitive(): void
    {
        self::assertSame([], $this->validateReason(['status' => 'CANCELADO']));
    }

    public function testMissingReferenceFieldMeansConditionNotMet(): void
    {
        self::assertSame([], $this->validateReason(['outro' => 'x']));
        self::assertSame([], $this->validateReason(['outro' => 'x', 'motivo' => '']));
    }

    public function testNullAndArrayReferenceNeverMatch(): void
    {
        self::assertSame([], $this->validateReason(['status' => null], 'requiredIf:status;'));
        self::assertSame([], $this->validateReason(['status' => ['cancelado']]));
    }

    public function testNumericReferenceIsComparedAsText(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['tipo' => 2], self::RULE_TYPE_TWO_OR_THREE),
        );
        self::assertSame([], $this->validateReason(['tipo' => 1], self::RULE_TYPE_TWO_OR_THREE));
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['tipo' => '2'], self::RULE_TYPE_TWO_OR_THREE),
        );
    }

    public function testBooleanReferenceMatchesTextAndNumericForms(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['ativo' => true], 'requiredIf:ativo;true;x'),
        );
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['ativo' => true], 'requiredIf:ativo;1;x'),
        );
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['ativo' => false], 'requiredIf:ativo;false;x'),
        );
        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validateReason(['ativo' => false], 'requiredIf:ativo;0;x'),
        );
        self::assertSame([], $this->validateReason(['ativo' => false], 'requiredIf:ativo;true;1'));
    }

    public function testCustomMessageIsUsedWhenFieldIsPresentButEmpty(): void
    {
        $errors = $this->validateReason(
            ['status' => 'cancelado', 'motivo' => ''],
            'requiredIf:status;cancelado, Informe o motivo do cancelamento',
        );

        self::assertSame(['motivo' => 'Informe o motivo do cancelamento'], $errors);
    }

    public function testCustomMessageIsUsedWhenFieldIsMissing(): void
    {
        $errors = $this->validateReason(
            ['status' => 'cancelado'],
            'requiredIf:status;cancelado, Informe o motivo do cancelamento',
        );

        self::assertSame(['motivo' => 'Informe o motivo do cancelamento'], $errors);
    }

    public function testCustomMessageInAnotherSegmentAppliesToPresentAndMissingField(): void
    {
        $rule = 'requiredIf:status;cancelado|min:3,Mensagem global';

        self::assertSame(
            ['motivo' => 'Mensagem global'],
            $this->validateReason(['status' => 'cancelado', 'motivo' => ''], $rule),
        );
        self::assertSame(['motivo' => 'Mensagem global'], $this->validateReason(['status' => 'cancelado'], $rule));
    }

    public function testOptionalTakesPrecedenceForPresentAndMissingField(): void
    {
        $rule = 'optional|' . self::RULE_CANCELED;

        self::assertSame([], $this->validateReason(['status' => 'cancelado', 'motivo' => ''], $rule));
        self::assertSame([], $this->validateReason(['status' => 'cancelado'], $rule));
    }

    public function testRuleNameIsExposedAsPublicConstant(): void
    {
        self::assertSame('requiredIf', Validator::RULE_REQUIRED_IF);
        self::assertArrayHasKey(Validator::RULE_REQUIRED_IF, Validator::functionsValidation());
    }

    public function testRuleWithoutParametersIsReportedAsInvalid(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_INVALID_RULE],
            $this->validateReason(['status' => 'cancelado', 'motivo' => 'x'], 'requiredIf'),
        );
        self::assertSame(
            ['motivo' => self::MESSAGE_INVALID_RULE],
            $this->validateReason(['status' => 'x'], 'requiredIf'),
        );
    }

    public function testRuleWithoutValuesIsReportedAsInvalid(): void
    {
        self::assertSame(
            ['motivo' => self::MESSAGE_INVALID_RULE],
            $this->validateReason(['status' => 'cancelado', 'motivo' => 'x'], 'requiredIf:status'),
        );
        self::assertSame(
            ['motivo' => self::MESSAGE_INVALID_RULE],
            $this->validateReason(['status' => 'cancelado'], 'requiredIf:status'),
        );
    }

    public function testInvalidRuleUsesCustomMessageWhenProvided(): void
    {
        $errors = $this->validateReason(['status' => 'x', 'motivo' => 'y'], 'requiredIf:status, Regra errada');

        self::assertSame(['motivo' => 'Regra errada'], $errors);
    }

    public function testRulesInJsonFormat(): void
    {
        $rule = '{"requiredIf":["status","cancelado","suspenso"]}';

        self::assertSame(['motivo' => self::MESSAGE_MISSING], $this->validateReason(['status' => 'suspenso'], $rule));
        self::assertSame(
            ['motivo' => self::MESSAGE_MANDATORY],
            $this->validateReason(['status' => 'cancelado', 'motivo' => ''], $rule),
        );
        self::assertSame([], $this->validateReason(['status' => 'ativo'], $rule));
    }

    public function testEachItemOfAListIsEvaluatedWithItsOwnData(): void
    {
        $data = [
            ['status' => 'cancelado', 'motivo' => 'Desistiu'],
            ['status' => 'ativo'],
            ['status' => 'cancelado'],
        ];

        $errors = $this->validate($data, ['motivo' => self::RULE_CANCELED]);

        self::assertSame(['motivo' => self::MESSAGE_MISSING], $errors);
    }

    public function testReferenceIsResolvedAtTheSameLevelOfTheRule(): void
    {
        $rules = ['cliente' => ['motivo' => self::RULE_CANCELED]];

        self::assertSame(
            ['motivo' => self::MESSAGE_MISSING],
            $this->validate(['cliente' => ['status' => 'cancelado']], $rules),
        );
        // `status` fora do objeto `cliente` não é visto pela regra.
        self::assertSame([], $this->validate(['status' => 'cancelado', 'cliente' => ['nome' => 'A']], $rules));
    }

    public function testRequiredKeepsWorkingNextToRequiredIfFields(): void
    {
        $rules = ['nome' => 'required', 'motivo' => self::RULE_CANCELED];

        self::assertSame(
            ['nome' => 'O campo obrigatório nome não foi encontrado nos dados de entrada!'],
            $this->validate(['status' => 'ativo'], $rules),
        );
    }

    public function testRulesAreIndependentBetweenFields(): void
    {
        $rules = [
            'motivo' => self::RULE_CANCELED,
            'observacao' => 'requiredIf:status;suspenso',
        ];

        self::assertSame(
            ['observacao' => 'O campo obrigatório observacao não foi encontrado nos dados de entrada!'],
            $this->validate(['status' => 'suspenso', 'motivo' => 'x'], $rules),
        );
    }
}
