<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;

class DynamoService implements ItemToArrayInterface, ArrayToItemInterface {
    protected $dynamo;
    protected static $instance;
    protected ArrayToItemInterface $array_to_item;
    protected ItemToArrayInterface $item_to_array;

    public function __construct(
        string                $region, string $dynamo_key, string $dynamo_secret,
        ?string               $endpoint = null, $debug = false,
        ?ItemToArrayInterface $item_to_array = null,
        ?ArrayToItemInterface $array_to_item = null
    ) {
        $credentials = new Credentials($dynamo_key, $dynamo_secret);

        $params = [
            'region' => $region,
            'credentials' => $credentials,
            'debug' => $debug,
            'version' => 'latest',
        ];
        if($endpoint) {
            $params['endpoint'] = $endpoint;
        }

        $this->dynamo = new DynamoDbClient($params);
        $this->item_to_array = $item_to_array ?? new ItemToArray();
        $this->array_to_item = $array_to_item ?? new ArrayToItem();
    }

    public function client(): DynamoDbClient {
        return $this->dynamo;
    }

    public function arrayToItem($item): array {
        return $this->array_to_item->arrayToItem($item);
    }

    public function parseArrayElement($value): array {
        return $this->array_to_item->parseArrayElement($value);
    }

    public function itemToArray(array $item): array {
        return $this->item_to_array->itemToArray($item);
    }

    public function parseItemProp($value_def) {
        return $this->item_to_array->parseItemProp($value_def);
    }
}
