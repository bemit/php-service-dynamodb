<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers \Bemit\DynamoDB\DynamoService
 */
final class DynamoServiceTest extends TestCase {
    public function testBasicIntegration(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret', 'localhost:8000');
        $client = $sercice->client();
        $this->assertInstanceOf(\Aws\DynamoDb\DynamoDbClient::class, $client);
    }

    public function testBasicConvertToItem(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->toItem(['some-key' => 'the-text']);
        $this->assertEquals(['some-key' => ['S' => 'the-text']], $res);
    }

    public function testBasicConvertFromItem(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->fromItem(['some-key' => ['S' => 'the-text']]);
        $this->assertEquals(['some-key' => 'the-text'], $res);
    }

    public function testBasicConvertToItemValue(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->toItemValue('the-text');
        $this->assertEquals(['S' => 'the-text'], $res);
    }

    public function testBasicConvertFromItemValue(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->fromItemValue(['S' => 'the-text']);
        $this->assertEquals('the-text', $res);
    }
}
