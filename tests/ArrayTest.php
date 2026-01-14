<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Arrays;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ArrayTest extends TestCase
{
    private const VEGETABLE_RUCULA = 'Rúcula';

    private array $fruitArray;
    private array $simpleArray;

    private function isValidXml(string $xmlString): bool
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $isValid = $dom->loadXML($xmlString);
        libxml_clear_errors();

        return $isValid;
    }

    private function assertXmlFrutas(SimpleXMLElement $xml): void
    {
        self::assertSame('Maçã', (string) $xml->frutas->fruta_1);
        self::assertSame('Pêra', (string) $xml->frutas->fruta_2);
        self::assertSame('Uva', (string) $xml->frutas->fruta_4);
        self::assertCount(4, $xml->frutas->children());
    }

    private function assertXmlVerduras(SimpleXMLElement $xml): void
    {
        self::assertSame(self::VEGETABLE_RUCULA, (string) $xml->verduras->verdura_1);
        self::assertSame('Acelga', (string) $xml->verduras->verdura_2);
        self::assertSame('Alface', (string) $xml->verduras->verdura_3);
        self::assertCount(3, $xml->verduras->children());
    }

    protected function setUp(): void
    {
        $this->fruitArray = [
            'frutas' => [
                'fruta_1' => 'Maçã',
                'fruta_2' => 'Pêra',
                'fruta_3' => 'fruta',
                'fruta_4' => 'Uva',
            ],
            'verduras' => ['verdura_1' => self::VEGETABLE_RUCULA, 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
            'legume' => 'Tomate',
        ];

        $this->simpleArray = ['primeiro' => 15, 'segundo' => 25];
    }

    public function testSearchKey(): void
    {
        self::assertIsInt(Arrays::searchKey($this->simpleArray, 'primeiro'));
        self::assertNull(Arrays::searchKey($this->simpleArray, 'nao-existe'));
    }

    public function testRenameKey(): void
    {
        $array = ['primeiro' => 10, 'segundo' => 20];
        self::assertTrue(Arrays::renameKey($array, 'primeiro', 'novoNome'));
        self::assertFalse(Arrays::renameKey($array, 'nao-existe', 'novoNome'));
    }

    public function testCheckExistIndexByValue(): void
    {
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Tomate'));
        self::assertFalse(Arrays::checkExistIndexByValue($this->fruitArray, 'nao-existe'));
    }

    public function testFindValueByKey(): void
    {
        self::assertIsArray(Arrays::findValueByKey($this->fruitArray, 'fruta_2'));
    }

    public function testFindIndexByValue(): void
    {
        self::assertIsArray(Arrays::findIndexByValue($this->fruitArray, self::VEGETABLE_RUCULA));
    }

    public function testConvertArrayToXml(): void
    {
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($this->fruitArray, $xml);

        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));

        self::assertTrue(isset($xml->frutas));
        self::assertTrue(isset($xml->verduras));
        self::assertTrue(isset($xml->legume));

        $this->assertXmlFrutas($xml);
        $this->assertXmlVerduras($xml);

        self::assertSame('Tomate', (string) $xml->legume);
    }

    public function testConvertJsonIndexToArray(): void
    {
        $array = $this->fruitArray;
        $array['verduras'] = '{"verdura_1": "' . self::VEGETABLE_RUCULA .
            '", "verdura_2": "Acelga", "verdura_3": "Alface"}';

        Arrays::convertJsonIndexToArray($array);

        self::assertIsArray($array);
        self::assertIsArray($array['verduras']);
        self::assertSame(self::VEGETABLE_RUCULA, $array['verduras']['verdura_1']);
    }

    public function testCheckExistsIndexArrayRecursive(): void
    {
        $array = [
            'pessoa' => [
                'pedidos' => ['pedido1', 'pedido2'],
                'categorias' => [
                    'subcategorias' => ['subcategoria1' => 'valor teste'],
                ],
            ],
        ];
        self::assertTrue(Arrays::checkExistIndexArrayRecursive($array, 'subcategoria1'));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive($array, 'mercado'));
    }

    public function testCheckExistsIndexArrayRecursiveWithNull(): void
    {
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(null, 'key'));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(['key' => 'value'], null));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(null, null));
    }

    public function testSearchKeyReturnsCorrectPosition(): void
    {
        self::assertSame(0, Arrays::searchKey($this->simpleArray, 'primeiro'));
        self::assertSame(1, Arrays::searchKey($this->simpleArray, 'segundo'));
    }

    public function testRenameKeyPreservesOrder(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        Arrays::renameKey($array, 'b', 'novo_b');
        $keys = array_keys($array);
        self::assertSame(['a', 'novo_b', 'c'], $keys);
        self::assertSame(2, $array['novo_b']);
    }

    public function testCheckExistIndexByValueInNestedArray(): void
    {
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Maçã'));
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Acelga'));
        self::assertFalse(Arrays::checkExistIndexByValue($this->fruitArray, 'Banana'));
    }

    public function testFindValueByKeyCaseInsensitive(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'FRUTA_1');
        self::assertNotEmpty($result);
    }

    public function testFindValueByKeyNested(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'verdura_1');
        self::assertArrayHasKey('verduras', $result);
    }

    public function testFindValueByKeyNotFound(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'nao_existe');
        self::assertEmpty($result);
    }

    public function testFindIndexByValueWithInt(): void
    {
        $array = ['a' => 1, 'b' => 2, 'nested' => ['c' => 3]];
        $result = Arrays::findIndexByValue($array, 2);
        self::assertArrayHasKey('b', $result);
        self::assertSame(2, $result['b']);
    }

    public function testFindIndexByValueWithBool(): void
    {
        $array = ['ativo' => true, 'inativo' => false];
        $result = Arrays::findIndexByValue($array, true);
        self::assertArrayHasKey('ativo', $result);
    }

    public function testFindIndexByValueNested(): void
    {
        $array = ['nivel1' => ['nivel2' => ['chave' => 'valor_buscado']]];
        $result = Arrays::findIndexByValue($array, 'valor_buscado');
        self::assertNotEmpty($result);
        self::assertArrayHasKey('nivel1', $result);
    }

    public function testFindIndexByValueNotFound(): void
    {
        $result = Arrays::findIndexByValue($this->fruitArray, 'nao_existe');
        self::assertEmpty($result);
    }

    public function testConvertArrayToXmlWithAttrKey(): void
    {
        $array = [
            ['@attr' => 'item', 'nome' => 'Produto 1'],
            ['@attr' => 'item', 'nome' => 'Produto 2'],
        ];
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($array, $xml);
        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));
        self::assertStringContainsString('<item>', $xmlString);
    }

    public function testConvertArrayToXmlWithSpecialChars(): void
    {
        $array = ['texto' => 'Valor com <tag> & "aspas"'];
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($array, $xml);
        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));
    }

    public function testConvertJsonIndexToArrayInvalidJson(): void
    {
        $array = ['campo' => 'texto normal não é json'];
        Arrays::convertJsonIndexToArray($array);
        self::assertSame('texto normal não é json', $array['campo']);
    }

    public function testConvertJsonIndexToArrayEmptyString(): void
    {
        $array = ['campo' => ''];
        Arrays::convertJsonIndexToArray($array);
        self::assertSame('', $array['campo']);
    }

    public function testConvertJsonIndexToArrayNestedJson(): void
    {
        $array = [
            'nivel1' => [
                'dados' => '{"chave": "valor"}',
            ],
        ];
        Arrays::convertJsonIndexToArray($array);
        self::assertIsArray($array['nivel1']['dados']);
        self::assertSame('valor', $array['nivel1']['dados']['chave']);
    }
}
