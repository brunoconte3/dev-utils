<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DevUtils\ValidateCart;

class UnitValidateCartTest extends TestCase
{
    public function testVisaValidNumbers()
    {
        $this->assertTrue(ValidateCart::isVisa('4111111111111111'));
        $this->assertTrue(ValidateCart::isVisa('4012888888881881'));
        $this->assertTrue(ValidateCart::isVisa('4222222222222'));
    }

    public function testVisaInvalidNumbers()
    {
        $this->assertFalse(ValidateCart::isVisa('5111111111111111'));
        $this->assertFalse(ValidateCart::isVisa('1234567890123'));
        $this->assertFalse(ValidateCart::isVisa('411111111111'));
    }

    public function testMastercardValidNumbers()
    {
        $this->assertTrue(ValidateCart::isMastercard('5555555555554444'));
        $this->assertTrue(ValidateCart::isMastercard('2221000000000009'));
    }

    public function testMastercardInvalidNumbers()
    {
        $this->assertFalse(ValidateCart::isMastercard('4111111111111111'));
        $this->assertFalse(ValidateCart::isMastercard('340000000000009'));
    }

    public function testEloValidNumbers()
    {
        $this->assertTrue(ValidateCart::isElo('4011780000000000'));
        $this->assertTrue(ValidateCart::isElo('5066991111111118'));
        $this->assertTrue(ValidateCart::isElo('6500000000000000'));
    }

    public function testEloInvalidNumbers()
    {
        $this->assertFalse(ValidateCart::isElo('4111111111111111'));
        $this->assertFalse(ValidateCart::isElo('340000000000009'));
        $this->assertFalse(ValidateCart::isElo('1234567890123456'));
    }

    public function testHipercardValidNumbers()
    {
        $this->assertTrue(ValidateCart::isHipercard('6062825624254001'));
        $this->assertTrue(ValidateCart::isHipercard('3841001111222233334'));
    }

    public function testHipercardInvalidNumbers()
    {
        $this->assertFalse(ValidateCart::isHipercard('4111111111111111'));
        $this->assertFalse(ValidateCart::isHipercard('340000000000009'));
    }

    public function testAmexValidNumbers()
    {
        $this->assertTrue(ValidateCart::isAmex('371449635398431'));
        $this->assertTrue(ValidateCart::isAmex('378282246310005'));
    }

    public function testAmexInvalidNumbers()
    {
        $this->assertFalse(ValidateCart::isAmex('4111111111111111'));
        $this->assertFalse(ValidateCart::isAmex('5555555555554444'));
    }

    public function testValidCvv()
    {
        $this->assertTrue(ValidateCart::isValidCvv('123'));
        $this->assertTrue(ValidateCart::isValidCvv('1234'));
    }

    public function testInvalidCvv()
    {
        $this->assertFalse(ValidateCart::isValidCvv('12'));
        $this->assertFalse(ValidateCart::isValidCvv('12345'));
        $this->assertFalse(ValidateCart::isValidCvv('abc'));
    }

    public function testVisaWithPunctuation()
    {
        $this->assertTrue(ValidateCart::isVisa('4111 1111 1111 1111'));
        $this->assertTrue(ValidateCart::isVisa('4111-1111-1111-1111'));
        $this->assertTrue(ValidateCart::isVisa('4111.1111.1111.1111'));
    }

    public function testEloWithPunctuation()
    {
        $this->assertTrue(ValidateCart::isElo('4011 7800 0000 0000'));
        $this->assertTrue(ValidateCart::isElo('4011-7800-0000-0000'));
        $this->assertTrue(ValidateCart::isElo('4011.7800.0000.0000'));
    }

    public function testCvvWithPunctuation()
    {
        $this->assertTrue(ValidateCart::isValidCvv('1 2 3'));
        $this->assertTrue(ValidateCart::isValidCvv('1-2-3'));
        $this->assertTrue(ValidateCart::isValidCvv('1.2.3'));
    }

    public function testMaskWithPunctuation()
    {
        $this->assertEquals('4111-1111-1111-1111', ValidateCart::maskCard('4111 1111 1111 1111', '####-####-####-####'));
        $this->assertEquals('4111-1111-1111-1111', ValidateCart::maskCard('4111-1111-1111-1111', '####-####-####-####'));
        $this->assertEquals('4111-1111-1111-1111', ValidateCart::maskCard('4111.1111.1111.1111', '####-####-####-####'));
    }

    public function testGetBrand()
    {
        $this->assertEquals('Visa', ValidateCart::getBrand('4111111111111111'));
        $this->assertEquals('Mastercard', ValidateCart::getBrand('5555555555554444'));
        $this->assertEquals('Elo', ValidateCart::getBrand('4011780000000000'));
        $this->assertEquals('Hipercard', ValidateCart::getBrand('6062825624254001'));
        $this->assertEquals('Amex', ValidateCart::getBrand('371449635398431'));
        $this->assertEquals('Desconhecida', ValidateCart::getBrand('1234567890123456'));
    }
} 