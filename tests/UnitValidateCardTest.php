<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DevUtils\ValidateCard;
use DevUtils\Format;

class UnitValidateCardTest extends TestCase
{
    public function testVisaValidNumbers()
    {
        $this->assertTrue(ValidateCard::isVisa('4111111111111111'));
        $this->assertTrue(ValidateCard::isVisa('4012888888881881'));
        $this->assertTrue(ValidateCard::isVisa('4222222222222'));
    }

    public function testVisaInvalidNumbers()
    {
        $this->assertFalse(ValidateCard::isVisa('5111111111111111'));
        $this->assertFalse(ValidateCard::isVisa('1234567890123'));
        $this->assertFalse(ValidateCard::isVisa('411111111111'));
    }

    public function testMastercardValidNumbers()
    {
        $this->assertTrue(ValidateCard::isMastercard('5555555555554444'));
        $this->assertTrue(ValidateCard::isMastercard('2221000000000009'));
    }

    public function testMastercardInvalidNumbers()
    {
        $this->assertFalse(ValidateCard::isMastercard('4111111111111111'));
        $this->assertFalse(ValidateCard::isMastercard('340000000000009'));
    }

    public function testEloValidNumbers()
    {
        $this->assertTrue(ValidateCard::isElo('4011780000000000'));
        $this->assertTrue(ValidateCard::isElo('5066991111111118'));
        $this->assertTrue(ValidateCard::isElo('6500000000000000'));
    }

    public function testEloInvalidNumbers()
    {
        $this->assertFalse(ValidateCard::isElo('4111111111111111'));
        $this->assertFalse(ValidateCard::isElo('340000000000009'));
        $this->assertFalse(ValidateCard::isElo('1234567890123456'));
    }

    public function testHipercardValidNumbers()
    {
        $this->assertTrue(ValidateCard::isHipercard('6062825624254001'));
        $this->assertTrue(ValidateCard::isHipercard('3841001111222233334'));
    }

    public function testHipercardInvalidNumbers()
    {
        $this->assertFalse(ValidateCard::isHipercard('4111111111111111'));
        $this->assertFalse(ValidateCard::isHipercard('340000000000009'));
    }

    public function testAmexValidNumbers()
    {
        $this->assertTrue(ValidateCard::isAmex('371449635398431'));
        $this->assertTrue(ValidateCard::isAmex('378282246310005'));
    }

    public function testAmexInvalidNumbers()
    {
        $this->assertFalse(ValidateCard::isAmex('4111111111111111'));
        $this->assertFalse(ValidateCard::isAmex('5555555555554444'));
    }

    public function testValidCvv()
    {
        $this->assertTrue(ValidateCard::isValidCvv('123'));
        $this->assertTrue(ValidateCard::isValidCvv('1234'));
    }

    public function testInvalidCvv()
    {
        $this->assertFalse(ValidateCard::isValidCvv('12'));
        $this->assertFalse(ValidateCard::isValidCvv('12345'));
        $this->assertFalse(ValidateCard::isValidCvv('abc'));
    }

    public function testVisaWithPunctuation()
    {
        $this->assertTrue(ValidateCard::isVisa('4111 1111 1111 1111'));
        $this->assertTrue(ValidateCard::isVisa('4111-1111-1111-1111'));
        $this->assertTrue(ValidateCard::isVisa('4111.1111.1111.1111'));
    }

    public function testEloWithPunctuation()
    {
        $this->assertTrue(ValidateCard::isElo('4011 7800 0000 0000'));
        $this->assertTrue(ValidateCard::isElo('4011-7800-0000-0000'));
        $this->assertTrue(ValidateCard::isElo('4011.7800.0000.0000'));
    }

    public function testCvvWithPunctuation()
    {
        $this->assertTrue(ValidateCard::isValidCvv('1 2 3'));
        $this->assertTrue(ValidateCard::isValidCvv('1-2-3'));
        $this->assertTrue(ValidateCard::isValidCvv('1.2.3'));
    }

    public function testMaskWithPunctuation()
    {
        $this->assertEquals('4111-1111-1111-1111', Format::mask('####-####-####-####', Format::onlyNumbers('4111 1111 1111 1111')));
        $this->assertEquals('4111-1111-1111-1111', Format::mask('####-####-####-####', Format::onlyNumbers('4111-1111-1111-1111')));
        $this->assertEquals('4111-1111-1111-1111', Format::mask('####-####-####-####', Format::onlyNumbers('4111.1111.1111.1111')));
    }

    public function testGetBrand()
    {
        $this->assertEquals('Visa', ValidateCard::getBrand('4111111111111111'));
        $this->assertEquals('Mastercard', ValidateCard::getBrand('5555555555554444'));
        $this->assertEquals('Elo', ValidateCard::getBrand('4011780000000000'));
        $this->assertEquals('Hipercard', ValidateCard::getBrand('6062825624254001'));
        $this->assertEquals('Amex', ValidateCard::getBrand('371449635398431'));
        $this->assertEquals('Desconhecida', ValidateCard::getBrand('1234567890123456'));
    }
} 