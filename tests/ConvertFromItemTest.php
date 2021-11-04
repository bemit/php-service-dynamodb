<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers \Bemit\DynamoDB\ConvertFromItem
 */
final class ConvertFromItemTest extends TestCase {
    public function testFromItemValueS(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['S' => 'some-text']);
        $this->assertEquals('some-text', $result);
    }

    public function testFromItemValueN(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['N' => '1.45']);
        $this->assertEquals(1.45, $result);
    }

    public function testFromItemValueBOOL(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['BOOL' => true]);
        $this->assertEquals(true, $result);
    }

    public function testFromItemValueNULL(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['NULL' => true]);
        $this->assertEquals(null, $result);
    }

    public function testFromItemValueM(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['M' => []]);
        $this->assertInstanceOf(stdClass::class, $result);
    }

    public function testFromItemValueMNested(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue([
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

    public function testFromItemValueSS(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue([
            'SS' => ['s1', 's2', 's3'],
        ]);
        $this->assertEquals(['s1', 's2', 's3'], $result);
    }

    public function testFromItemValueNS(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue([
            'NS' => ['1', '2.132435467', '3'],
        ]);
        $this->assertEquals([1, 2.132435467, 3], $result);
    }

    public function testFromItemValueL(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue(['L' => []]);
        $this->assertIsArray($result);
    }

    public function testFromItemValueLNested(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $result = $converter->fromItemValue([
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

    public function testFromItem(): void {
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $res = $converter->fromItem([
            'some-key' => [
                'L' => [
                    ['S' => 'e-1'],
                    ['S' => 'e-2'],
                ]
            ]
        ]);
        $this->assertEquals(['some-key' => ['e-1', 'e-2']], $res);
    }

    public function testFromItemInvalidMode(): void {
        $this->expectException(\Bemit\DynamoDB\InvalidItemTypeException::class);
        $converter = new \Bemit\DynamoDB\ConvertFromItem();
        $res = $converter->fromItemValue([
            'BINARY_MODE' => 'some-text-but-would-be-something-else'
        ]);
    }
}
