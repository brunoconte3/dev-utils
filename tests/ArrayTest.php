<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\Arrays;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class ArrayTest extends TestCase
{
    private const VEGETABLE_ARUGULA = 'Rúcula';

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

    private function assertXmlFruits(SimpleXMLElement $xml): void
    {
        self::assertSame('Maçã', (string) $xml->fruits->fruit_1);
        self::assertSame('Pêra', (string) $xml->fruits->fruit_2);
        self::assertSame('Uva', (string) $xml->fruits->fruit_4);
        self::assertCount(4, $xml->fruits->children());
    }

    private function assertXmlVegetables(SimpleXMLElement $xml): void
    {
        self::assertSame(self::VEGETABLE_ARUGULA, (string) $xml->vegetables->vegetable_1);
        self::assertSame('Acelga', (string) $xml->vegetables->vegetable_2);
        self::assertSame('Alface', (string) $xml->vegetables->vegetable_3);
        self::assertCount(3, $xml->vegetables->children());
    }

    protected function setUp(): void
    {
        $this->fruitArray = [
            'fruits' => [
                'fruit_1' => 'Maçã',
                'fruit_2' => 'Pêra',
                'fruit_3' => 'fruit',
                'fruit_4' => 'Uva',
            ],
            'legume' => 'Tomate',
            'vegetables' => [
                'vegetable_1' => self::VEGETABLE_ARUGULA,
                'vegetable_2' => 'Acelga',
                'vegetable_3' => 'Alface',
            ],
        ];

        $this->simpleArray = ['first' => 15, 'second' => 25];
    }

    public function testSearchKey(): void
    {
        self::assertIsInt(Arrays::searchKey($this->simpleArray, 'first'));
        self::assertNull(Arrays::searchKey($this->simpleArray, 'does-not-exist'));
    }

    public function testRenameKey(): void
    {
        $array = ['first' => 10, 'second' => 20];
        self::assertTrue(Arrays::renameKey($array, 'first', 'newName'));
        self::assertFalse(Arrays::renameKey($array, 'does-not-exist', 'newName'));
    }

    public function testCheckExistIndexByValue(): void
    {
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Tomate'));
        self::assertFalse(Arrays::checkExistIndexByValue($this->fruitArray, 'does-not-exist'));
    }

    public function testFindValueByKey(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'fruit_2');
        self::assertSame(['fruits' => ['fruit_2' => 'Pêra']], $result);
    }

    public function testFindIndexByValue(): void
    {
        $result = Arrays::findIndexByValue($this->fruitArray, self::VEGETABLE_ARUGULA);
        self::assertSame(['vegetables' => ['vegetable_1' => self::VEGETABLE_ARUGULA]], $result);
    }

    public function testConvertArrayToXml(): void
    {
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($this->fruitArray, $xml);

        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));

        self::assertTrue(isset($xml->fruits));
        self::assertTrue(isset($xml->vegetables));
        self::assertTrue(isset($xml->legume));

        $this->assertXmlFruits($xml);
        $this->assertXmlVegetables($xml);

        self::assertSame('Tomate', (string) $xml->legume);
    }

    public function testConvertJsonIndexToArray(): void
    {
        $array = $this->fruitArray;
        $array['vegetables'] = '{"vegetable_1": "' . self::VEGETABLE_ARUGULA .
            '", "vegetable_2": "Acelga", "vegetable_3": "Alface"}';

        Arrays::convertJsonIndexToArray($array);

        self::assertIsArray($array['vegetables']);
        self::assertSame(self::VEGETABLE_ARUGULA, $array['vegetables']['vegetable_1']);
    }

    public function testCheckExistsIndexArrayRecursive(): void
    {
        $array = [
            'person' => [
                'categories' => [
                    'subcategories' => ['subcategory1' => 'test value'],
                ],
                'orders' => ['order1', 'order2'],
            ],
        ];
        self::assertTrue(Arrays::checkExistIndexArrayRecursive($array, 'subcategory1'));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive($array, 'market'));
    }

    public function testCheckExistsIndexArrayRecursiveWithNull(): void
    {
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(null, 'key'));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(['key' => 'value'], null));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive(null, null));
    }

    public function testSearchKeyReturnsCorrectPosition(): void
    {
        self::assertSame(0, Arrays::searchKey($this->simpleArray, 'first'));
        self::assertSame(1, Arrays::searchKey($this->simpleArray, 'second'));
    }

    public function testRenameKeyPreservesOrder(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        Arrays::renameKey($array, 'b', 'new_b');
        $keys = array_keys($array);
        self::assertSame(['a', 'new_b', 'c'], $keys);
        self::assertSame(2, $array['new_b']);
    }

    public function testCheckExistIndexByValueInNestedArray(): void
    {
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Maçã'));
        self::assertTrue(Arrays::checkExistIndexByValue($this->fruitArray, 'Acelga'));
        self::assertFalse(Arrays::checkExistIndexByValue($this->fruitArray, 'Banana'));
    }

    public function testFindValueByKeyCaseInsensitive(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'FRUIT_1');
        self::assertSame(['fruits' => ['fruit_1' => 'Maçã']], $result);
    }

    public function testFindValueByKeyNested(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'vegetable_1');
        self::assertArrayHasKey('vegetables', $result);
    }

    public function testFindValueByKeyNotFound(): void
    {
        $result = Arrays::findValueByKey($this->fruitArray, 'does_not_exist');
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
        $array = ['active' => true, 'inactive' => false];
        $result = Arrays::findIndexByValue($array, true);
        self::assertArrayHasKey('active', $result);
    }

    public function testFindIndexByValueNested(): void
    {
        $array = ['level1' => ['level2' => ['key' => 'searched_value']]];
        $result = Arrays::findIndexByValue($array, 'searched_value');
        self::assertNotEmpty($result);
        self::assertArrayHasKey('level1', $result);
    }

    public function testFindIndexByValueNotFound(): void
    {
        $result = Arrays::findIndexByValue($this->fruitArray, 'does_not_exist');
        self::assertEmpty($result);
    }

    public function testConvertArrayToXmlWithAttrKey(): void
    {
        $array = [
            ['@attr' => 'item', 'name' => 'Produto 1'],
            ['@attr' => 'item', 'name' => 'Produto 2'],
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
        $array = ['text' => 'Valor com <tag> & "aspas"'];
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($array, $xml);
        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));
    }

    public function testConvertJsonIndexToArrayInvalidJson(): void
    {
        $array = ['field' => 'texto normal não é json'];
        Arrays::convertJsonIndexToArray($array);
        self::assertSame('texto normal não é json', $array['field']);
    }

    public function testConvertJsonIndexToArrayEmptyString(): void
    {
        $array = ['field' => ''];
        Arrays::convertJsonIndexToArray($array);
        self::assertSame('', $array['field']);
    }

    public function testConvertJsonIndexToArrayNestedJson(): void
    {
        $array = [
            'level1' => [
                'data' => '{"key": "value"}',
            ],
        ];
        Arrays::convertJsonIndexToArray($array);
        $level1 = $array['level1'];
        self::assertIsArray($level1);
        $data = $level1['data'];
        self::assertIsArray($data);
        self::assertSame('value', $data['key']);
    }

    public function testRenameKeyToExistingKeyDoesNotOverwrite(): void
    {
        $array = ['a' => 1, 'b' => 2];

        self::assertFalse(Arrays::renameKey($array, 'a', 'b'));
        self::assertSame(['a' => 1, 'b' => 2], $array);
    }

    public function testRenameKeyToSameKeyIsNoOp(): void
    {
        $array = ['a' => 1, 'b' => 2];
        self::assertTrue(Arrays::renameKey($array, 'a', 'a'));
        self::assertSame(['a' => 1, 'b' => 2], $array);
    }

    public function testConvertArrayToXmlWithNumericKeysProducesValidXml(): void
    {
        $array = [0 => 'value', 1 => 'other'];
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($array, $xml);

        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));
        self::assertCount(2, $xml->item);
        self::assertSame('value', (string) $xml->item[0]);
        self::assertSame('other', (string) $xml->item[1]);
    }

    public function testConvertArrayToXmlWithNumericKeyAndArrayValue(): void
    {
        $array = [0 => ['name' => 'Produto']];
        $xml = new SimpleXMLElement('<root/>');
        Arrays::convertArrayToXml($array, $xml);

        $xmlString = $xml->asXML();
        self::assertIsString($xmlString);
        self::assertTrue($this->isValidXml($xmlString));
        self::assertSame('Produto', (string) $xml->item->name);
    }

    public function testFindIndexByValueUsesStrictComparison(): void
    {
        self::assertEmpty(Arrays::findIndexByValue(['x' => '2'], 2));
        self::assertArrayHasKey('x', Arrays::findIndexByValue(['x' => 2], 2));
    }

    public function testCheckExistIndexByValueCastsToString(): void
    {
        self::assertTrue(Arrays::checkExistIndexByValue(['n' => 15], '15'));
        self::assertTrue(Arrays::checkExistIndexByValue(['active' => true], '1'));
    }

    public function testConvertJsonIndexToArrayWithJsonList(): void
    {
        $array = ['data' => '[1, 2, 3]'];
        Arrays::convertJsonIndexToArray($array);
        self::assertIsArray($array['data']);
        self::assertSame([1, 2, 3], $array['data']);
    }

    public function testConvertJsonIndexToArrayKeepsJsonScalarAsString(): void
    {
        $array = ['number' => '123', 'boolean' => 'true'];
        Arrays::convertJsonIndexToArray($array);
        self::assertSame('123', $array['number']);
        self::assertSame('true', $array['boolean']);
    }

    public function testMethodsWithEmptyArray(): void
    {
        self::assertNull(Arrays::searchKey([], 'anything'));
        self::assertEmpty(Arrays::findValueByKey([], 'anything'));
        self::assertEmpty(Arrays::findIndexByValue([], 'anything'));
        self::assertFalse(Arrays::checkExistIndexByValue([], 'anything'));
        self::assertFalse(Arrays::checkExistIndexArrayRecursive([], 'anything'));
    }
}
