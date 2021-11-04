<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;

class DynamoService implements DynamoServiceInterface {

    protected DynamoDbClient $dynamo;
    protected ConvertFromItemInterface $from_item;
    protected ConvertToItemInterface $to_item;

    public function __construct(
        string                    $region, string $dynamo_key, string $dynamo_secret,
        ?string                   $endpoint = null, $debug = false,
        ?ConvertFromItemInterface $from_item = null,
        ?ConvertToItemInterface   $to_item = null
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
        $this->from_item = $from_item ?? new ConvertFromItem();
        $this->to_item = $to_item ?? new ConvertToItem();
    }

    public function client(): DynamoDbClient {
        return $this->dynamo;
    }

    /**
     * @throws InvalidTypeException
     * @throws \JsonException
     */
    public function toItem(array|\stdClass $item, array $schema = [], bool $ignore_nulls = false): array {
        return $this->to_item->toItem($item, $schema, $ignore_nulls);
    }

    /**
     * @throws InvalidTypeException
     * @throws \JsonException
     */
    public function toItemValue($value, ?string $type = null): array {
        return $this->to_item->toItemValue($value, $type);
    }

    /**
     * @throws \JsonException
     * @throws InvalidItemTypeException
     */
    public function fromItem(array $item, bool $enforce_object = false): array|\stdClass {
        return $this->from_item->fromItem($item, $enforce_object);
    }

    /**
     * @throws \JsonException
     * @throws InvalidItemTypeException
     */
    public function fromItemValue($value_def): float|string|bool|array|\stdClass|null {
        return $this->from_item->fromItemValue($value_def);
    }
}
