<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers \Bemit\DynamoDB\ArrayToItem
 */
final class ArrayToItemTest extends TestCase {
    public function testArrayElementS(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement('some-text');
        $this->assertEquals(['S' => 'some-text'], $result);
    }

    public function testArrayElementBOOL(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement(false);
        $this->assertEquals(['BOOL' => false], $result);
        $result = $a2i->parseArrayElement(true);
        $this->assertEquals(['BOOL' => true], $result);
    }

    public function testArrayElementM(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement(new stdClass());
        $this->assertEquals(['M' => []], $result);
    }

    public function testArrayElementMNested(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $data = new stdClass();
        $data->some_s = 'the-text';
        $data->nested = new stdClass();
        $data->nested->some_n_s = 'nested-text';
        $result = $a2i->parseArrayElement($data);
        $this->assertEquals(
            [
                'M' => [
                    'some_s' => ['S' => 'the-text'],
                    'nested' => [
                        'M' => [
                            'some_n_s' => ['S' => 'nested-text'],
                        ]
                    ],
                ]
            ],
            $result
        );
    }

    public function testArrayElementL(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement([]);
        $this->assertEquals(
            ['L' => []],
            $result
        );
    }

    public function testArrayElementLNested(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement([
            'the-text',
            1234,
            [
                'nested-text',
                [
                    'further-nested',
                ]
            ]
        ]);
        $this->assertEquals(
            [
                'L' => [
                    ['S' => 'the-text'],
                    ['N' => '1234'],
                    [
                        'L' => [
                            ['S' => 'nested-text'],
                            [
                                'L' => [
                                    ['S' => 'further-nested'],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $result
        );
    }

    public function testArrayElementLAssoc(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement([
            's-k' => 'a-1',
            0 => 'a-2',
        ]);
        $this->assertEquals(
            [
                'M' => [
                    's-k' => ['S' => 'a-1'],
                    0 => ['S' => 'a-2'],
                ]
            ],
            $result
        );
        $result = $a2i->parseArrayElement([
            0 => 'a-2',
            's-k' => 'a-1',
        ]);
        $this->assertEquals(
            [
                'M' => [
                    '0' => ['S' => 'a-2'],
                    's-k' => ['S' => 'a-1'],
                ]
            ],
            $result
        );
    }

    public function testArrayElementN(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement(1);
        $this->assertEquals(['N' => '1'], $result);
        $result = $a2i->parseArrayElement(1.0);
        $this->assertEquals(['N' => '1'], $result);
        $result = $a2i->parseArrayElement(1.5666666666);
        $this->assertEquals(['N' => '1.5666666666'], $result);
        $result = $a2i->parseArrayElement('1.0');
        $this->assertEquals(['S' => '1.0'], $result);
        $result = $a2i->parseArrayElement(5000000000);
        $this->assertEquals(['N' => 5000000000], $result);
    }

    public function testArrayElementNull(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement(null);
        $this->assertEquals(['NULL' => true], $result);
    }

    public function testArrayItem(): void {
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->arrayToItem([
            'the-text',
            1,
            false,
            new stdClass(),
        ]);
        $this->assertEquals([
            ['S' => 'the-text'],
            ['N' => '1'],
            ['BOOL' => false],
            ['M' => []],
        ], $result);
    }

    public function testArrayPropInvalidDataType(): void {
        $this->expectException(\Bemit\DynamoDB\InvalidTypeException::class);
        $a2i = new \Bemit\DynamoDB\ArrayToItem();
        $result = $a2i->parseArrayElement(new DateTime());
    }
}
