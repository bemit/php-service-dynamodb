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

    public function testBasicArrayToItem(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->arrayToItem(['some-key' => 'the-text']);
        $this->assertEquals(['some-key' => ['S' => 'the-text']], $res);
    }

    public function testBasicItemToArray(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->itemToArray(['some-key' => ['S' => 'the-text']]);
        $this->assertEquals(['some-key' => 'the-text'], $res);
    }

    public function testBasicArrayElement(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->parseArrayElement('the-text');
        $this->assertEquals(['S' => 'the-text'], $res);
    }

    public function testBasicItemProp(): void {
        $sercice = new \Bemit\DynamoDB\DynamoService('eu-central-1', 'somekey', 'somesecret');
        $res = $sercice->parseItemProp(['S' => 'the-text']);
        $this->assertEquals('the-text', $res);
    }
}
