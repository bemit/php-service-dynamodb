<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

use Aws\DynamoDb\DynamoDbClient;

interface DynamoServiceInterface extends ConvertFromItemInterface, ConvertToItemInterface {
    public function client(): DynamoDbClient;
}
