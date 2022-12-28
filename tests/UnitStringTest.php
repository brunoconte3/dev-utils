<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Validator;
use PHPUnit\Framework\TestCase;

class UnitStringTest extends TestCase
{
    public function testAlpha(): void
    {
        $array = ['testError' => 'a@', 'testValid' => 'aeiouAÉIÓÚ',];
        $rules = ['testError' => 'alpha', 'testValid' => 'alpha',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNoSpecial(): void
    {
        $array = ['testError' => 'aéiou', 'testValid' => 'aEiOU',];
        $rules = ['testError' => 'alphaNoSpecial', 'testValid' => 'alphaNoSpecial',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNum(): void
    {
        $array = ['testError' => 'a1B2Éí3@', 'testValid' => 'a1B2Éí3',];
        $rules = ['testError' => 'alphaNum', 'testValid' => 'alphaNum',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testAlphaNumNoSpecial(): void
    {
        $array = ['testError' => 'AeioÚ123', 'testValid' => 'AeioU123',];
        $rules = ['testError' => 'alphaNumNoSpecial', 'testValid' => 'alphaNumNoSpecial',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testRgbColor(): void
    {
        $array = ['testError' => '300, 50, 255', 'testValid' => '0, 43, 233',];
        $rules = ['testError' => 'rgbColor', 'testValid' => 'rgbColor',];

        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(1, $validator->getErros());
    }

    public function testValidateDdd(): void
    {
        $array = [
            'testErrorDddTwoDigits' => '60',
            'testValidTwoDigits' => '61',
            'testErrorDddTwoDigitsState' => '11',
            'testValidTwoDigitsState' => '44',
        ];
        $rules = [
            'testErrorDddTwoDigits' => 'ddd',
            'testValidTwoDigits' => 'ddd',
            'testErrorDddTwoDigitsState' => 'ddd:pr',
            'testValidTwoDigitsState' => 'ddd:pr',
        ];
        $validator = new Validator();
        $validator->set($array, $rules);
        self::assertCount(2, $validator->getErros());
    }
}
