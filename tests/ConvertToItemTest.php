<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers \Bemit\DynamoDB\ConvertToItem
 */
final class ConvertToItemTest extends TestCase {
    public function testToItemValueS(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue('some-text');
        $this->assertEquals(['S' => 'some-text'], $result);
    }

    public function testToItemValueBOOL(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(false);
        $this->assertEquals(['BOOL' => false], $result);
        $result = $converter->toItemValue(true);
        $this->assertEquals(['BOOL' => true], $result);
    }

    public function testToItemValueM(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(new stdClass());
        $this->assertEquals(['M' => []], $result);
    }

    public function testToItemValueMNested(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $data = new stdClass();
        $data->some_s = 'the-text';
        $data->nested = new stdClass();
        $data->nested->some_n_s = 'nested-text';
        $result = $converter->toItemValue($data);
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

    public function testToItemValueL(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue([]);
        $this->assertEquals(
            ['L' => []],
            $result
        );
    }

    public function testToItemValueLNested(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue([
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

    public function testToItemValueLAssoc(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue([
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
        $result = $converter->toItemValue([
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

    public function testToItemValueN(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(1);
        $this->assertEquals(['N' => '1'], $result);
        $result = $converter->toItemValue(1.0);
        $this->assertEquals(['N' => '1'], $result);
        $result = $converter->toItemValue(1.5666666666);
        $this->assertEquals(['N' => '1.5666666666'], $result);
        $result = $converter->toItemValue('1.0');
        $this->assertEquals(['S' => '1.0'], $result);
        $result = $converter->toItemValue(5000000000);
        $this->assertEquals(['N' => 5000000000], $result);
    }

    public function testToItemValueSS(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(['s1', 's2', 's3'], 'SS');
        $this->assertEquals(['SS' => ['s1', 's2', 's3']], $result);
    }

    public function testToItemValueNS(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue([1, 2, 3], 'NS');
        $this->assertEquals(['NS' => [1, 2, 3]], $result);
    }

    public function testToItemValueNull(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(null);
        $this->assertEquals(['NULL' => true], $result);
    }

    public function testToItem(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItem([
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

    public function testToItemSchema(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItem(
            [
                'k1' => 's1',
                'k2' => ['sa', 'sb', 'sc'],
            ],
            [
                'k2' => 'SS',
            ]
        );
        $this->assertEquals([
            'k1' => ['S' => 's1'],
            'k2' => ['SS' => ['sa', 'sb', 'sc']],
        ], $result);
    }

    public function testToItemIgnoreNulls(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItem(
            [
                'k1' => 's1',
                'k2' => null,
                'k3' => 'r3',
            ],
            [],
            true,
        );
        $this->assertEquals([
            'k1' => ['S' => 's1'],
            'k3' => ['S' => 'r3'],
        ], $result);
    }

    public function testToItemKeepNulls(): void {
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItem(
            [
                'k1' => 's1',
                'k2' => null,
                'k3' => 'r3',
            ],
            [],
        );
        $this->assertEquals([
            'k1' => ['S' => 's1'],
            'k2' => ['NULL' => true],
            'k3' => ['S' => 'r3'],
        ], $result);
    }

    public function testToItemValueTypeUndetectable(): void {
        $this->expectException(\Bemit\DynamoDB\InvalidTypeException::class);
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue(new DateTime());
    }

    public function testToItemValueTypeNotConvertable(): void {
        $this->expectException(\Bemit\DynamoDB\InvalidTypeException::class);
        $converter = new \Bemit\DynamoDB\ConvertToItem();
        $result = $converter->toItemValue('some-text', 'LS');
    }
}
