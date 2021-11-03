<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers \Bemit\DynamoDB\ItemToArray
 */
final class ItemToArrayTest extends TestCase {
    public function testItemElementS(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['S' => 'some-text']);
        $this->assertEquals('some-text', $result);
    }

    public function testItemElementN(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['N' => '1.45']);
        $this->assertEquals(1.45, $result);
    }

    public function testItemElementBOOL(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['BOOL' => true]);
        $this->assertEquals(true, $result);
    }

    public function testItemElementNULL(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['NULL' => true]);
        $this->assertEquals(null, $result);
    }

    public function testItemElementM(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['M' => []]);
        $this->assertInstanceOf(stdClass::class, $result);
    }

    public function testItemElementMNested(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp([
            'M' => [
                'id_1' => ['S' => 'a-1'],
                'id_2' => ['N' => '2'],
                'id_3' => ['M' => []],
            ]
        ]);
        $should = new stdClass();
        $should->id_1 = 'a-1';
        $should->id_2 = 2;
        $should->id_3 = new stdClass();
        $this->assertEquals($should, $result);
    }

    public function testItemElementL(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp(['L' => []]);
        $this->assertIsArray($result);
    }

    public function testItemElementLNested(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $result = $a2i->parseItemProp([
            'L' => [
                ['S' => 'a-1'],
                ['S' => 'a-2'],
            ],
        ]);
        $this->assertEquals([
            'a-1',
            'a-2',
        ], $result);
    }

    public function testItem(): void {
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $res = $a2i->itemToArray([
            'some-key' => [
                'L' => [
                    ['S' => 'e-1'],
                    ['S' => 'e-2'],
                ]
            ]
        ]);
        $this->assertEquals(['some-key' => ['e-1', 'e-2']], $res);
    }

    public function testItemInvalidMode(): void {
        $this->expectException(\Bemit\DynamoDB\InvalidModeException::class);
        $a2i = new \Bemit\DynamoDB\ItemToArray();
        $res = $a2i->parseItemProp([
            'BINARY_MODE' => 'some-text-but-would-be-something-else'
        ]);
    }
}
